<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToListContents;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;

/**
 * Flysystem adapter that keeps file bytes in the `file_storage` table.
 *
 * Vercel's runtime filesystem is read-only, so uploads written to disk are
 * lost between requests — this makes `Storage::disk('public')` persist in the
 * database instead (PUBLIC_DISK=db). Files that were committed to the repo
 * under public/storage remain readable through the public/ fallback, so
 * re-cropping and reading the couple's shipped photos keep working.
 */
class DbFilesystemAdapter implements FilesystemAdapter
{
    public const TABLE = 'file_storage';

    /**
     * Normalise a path and refuse traversal — this gets called with
     * user-supplied URL segments, so '..' never passes through.
     */
    private function safe(string $path): ?string
    {
        $parts = [];
        foreach (explode('/', str_replace('\\', '/', $path)) as $seg) {
            if ($seg === '' || $seg === '.') {
                continue;
            }
            if ($seg === '..') {
                return null;
            }
            $parts[] = $seg;
        }

        return $parts ? implode('/', $parts) : null;
    }

    private function row(string $path): ?object
    {
        return DB::table(self::TABLE)->where('path', $path)->first();
    }

    private function bytes(object $row): string
    {
        $contents = $row->contents;
        if (is_resource($contents)) {
            $contents = stream_get_contents($contents) ?: '';
        }

        return (string) $contents;
    }

    /** Committed files live at public/storage/<path>. */
    private function fallbackFile(string $path): ?string
    {
        $file = public_path('storage/' . $path);

        return (is_file($file) && is_readable($file)) ? $file : null;
    }

    private function guessMime(string $contents): ?string
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                return finfo_buffer($finfo, $contents, FILEINFO_MIME_TYPE) ?: null;
            }
        }

        return null;
    }

    public function fileExists(string $path): bool
    {
        $path = $this->safe($path);
        if ($path === null) {
            return false;
        }

        return $this->row($path) !== null || $this->fallbackFile($path) !== null;
    }

    public function directoryExists(string $path): bool
    {
        $path = $this->safe($path) ?? '';
        if ($path === '') {
            return true;
        }
        $found = DB::table(self::TABLE)
            ->whereRaw("path LIKE ? ESCAPE '\\'", [$this->likeEscape($path) . '/%'])
            ->exists();

        return $found || is_dir(public_path('storage/' . $path));
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $path = $this->safe($path);
        if ($path === null) {
            throw UnableToWriteFile::atLocation($path ?? '', 'Unsafe path');
        }

        try {
            DB::table(self::TABLE)->upsert(
                [
                    'path' => $path,
                    'contents' => $contents,
                    'mime' => $this->guessMime($contents),
                    'size' => strlen($contents),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                ['path'],
                ['contents', 'mime', 'size', 'updated_at']
            );
        } catch (\Throwable $e) {
            throw UnableToWriteFile::atLocation($path, $e->getMessage(), $e);
        }
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $bytes = '';
        if (is_resource($contents)) {
            while (!feof($contents)) {
                $bytes .= fread($contents, 1024 * 1024);
            }
        } elseif (is_string($contents)) {
            $bytes = $contents;
        }

        $this->write($path, $bytes, $config);
    }

    public function read(string $path): string
    {
        $clean = $this->safe($path);
        if ($clean !== null) {
            $row = $this->row($clean);
            if ($row) {
                return $this->bytes($row);
            }
            $file = $this->fallbackFile($clean);
            if ($file !== null) {
                $bytes = file_get_contents($file);
                if ($bytes !== false) {
                    return $bytes;
                }
            }
        }

        throw UnableToReadFile::fromLocation($path, 'No such file in storage');
    }

    public function readStream(string $path)
    {
        $bytes = $this->read($path);
        $stream = fopen('php://temp', 'r+b');
        fwrite($stream, $bytes);
        rewind($stream);

        return $stream;
    }

    public function delete(string $path): void
    {
        $clean = $this->safe($path);
        if ($clean === null) {
            return;
        }
        try {
            DB::table(self::TABLE)->where('path', $clean)->delete();
        } catch (\Throwable $e) {
            throw UnableToDeleteFile::atLocation($clean, $e->getMessage(), $e);
        }
    }

    public function deleteDirectory(string $path): void
    {
        $clean = $this->safe($path) ?? '';
        $prefix = $clean === '' ? '' : $clean . '/';
        try {
            if ($prefix === '') {
                DB::table(self::TABLE)->delete();
            } else {
                DB::table(self::TABLE)
                    ->whereRaw("path LIKE ? ESCAPE '\\'", [$this->likeEscape($prefix) . '%'])
                    ->delete();
            }
        } catch (\Throwable $e) {
            throw UnableToDeleteDirectory::atLocation($path, $e->getMessage(), $e);
        }
    }

    public function createDirectory(string $path, Config $config): void
    {
        // Directories are implicit — files carry their own path.
    }

    public function setVisibility(string $path, string $visibility): void
    {
        // Everything stored here is public.
    }

    public function visibility(string $path): FileAttributes
    {
        return new FileAttributes($path, null, 'public');
    }

    public function mimeType(string $path): FileAttributes
    {
        $row = $this->row($this->safe($path) ?? '');
        if ($row && $row->mime) {
            return new FileAttributes($path, (int) $row->size, 'public', null, $row->mime);
        }

        return new FileAttributes($path, null, 'public', null, 'application/octet-stream');
    }

    public function lastModified(string $path): FileAttributes
    {
        $row = $this->row($this->safe($path) ?? '');
        $mtime = $row && $row->updated_at ? strtotime((string) $row->updated_at) : null;

        return new FileAttributes($path, null, 'public', $mtime ?: null);
    }

    public function fileSize(string $path): FileAttributes
    {
        $row = $this->row($this->safe($path) ?? '');

        return new FileAttributes($path, $row ? (int) $row->size : null, 'public');
    }

    public function listContents(string $path, bool $deep): iterable
    {
        $clean = $this->safe($path) ?? '';
        $prefix = $clean === '' ? '' : $clean . '/';
        $query = DB::table(self::TABLE)->orderBy('path');
        if ($prefix !== '') {
            $query->whereRaw("path LIKE ? ESCAPE '\\'", [$this->likeEscape($prefix) . '%']);
        }

        foreach ($query->cursor() as $row) {
            if (!$deep && $prefix !== '') {
                $rest = substr($row->path, strlen($prefix));
                if (str_contains($rest, '/')) {
                    continue;
                }
            }

            yield new FileAttributes(
                $row->path,
                (int) $row->size,
                'public',
                $row->updated_at ? strtotime((string) $row->updated_at) : null,
                $row->mime
            );
        }
    }

    public function move(string $source, string $destination, Config $config): void
    {
        $bytes = $this->read($source);
        $this->write($destination, $bytes, $config);
        $this->delete($source);
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        try {
            $bytes = $this->read($source);
            $this->write($destination, $bytes, $config);
        } catch (\Throwable $e) {
            throw UnableToCopyFile::because($e->getMessage(), $source, $destination);
        }
    }

    private function likeEscape(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
