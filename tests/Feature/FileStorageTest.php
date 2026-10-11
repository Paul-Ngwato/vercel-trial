<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * PUBLIC_DISK=db keeps uploads in the file_storage table so they survive
 * on hosts with a read-only filesystem (Vercel). The /storage/{path} route
 * serves misses through that disk.
 */
class FileStorageTest extends TestCase
{
    use RefreshDatabase;

    /** 1×1 transparent PNG. */
    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    private function useDbDisk(): void
    {
        config(['filesystems.disks.public' => [
            'driver' => 'dbfiles',
            'url' => 'http://localhost/storage',
            'throw' => true,
        ]]);
    }

    public function test_db_disk_round_trips_a_file(): void
    {
        $this->useDbDisk();
        $disk = Storage::disk('public');
        $bytes = base64_decode(self::PNG);

        $disk->put('photos/unit-test.png', $bytes);

        $this->assertDatabaseHas('file_storage', [
            'path' => 'photos/unit-test.png',
            'size' => strlen($bytes),
        ]);
        $this->assertTrue($disk->exists('photos/unit-test.png'));
        $this->assertSame($bytes, $disk->get('photos/unit-test.png'));
        $this->assertSame('image/png', $disk->mimeType('photos/unit-test.png'));
        $this->assertSame(strlen($bytes), $disk->size('photos/unit-test.png'));
    }

    public function test_storage_route_serves_database_files(): void
    {
        $this->useDbDisk();
        Storage::disk('public')->put('photos/route-test.png', base64_decode(self::PNG));

        $response = $this->get('/storage/photos/route-test.png');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/png');
        $response->assertHeader('Content-Disposition', 'inline; filename="route-test.png"');
        $this->assertSame(base64_decode(self::PNG), $response->getContent());
    }

    public function test_storage_route_404s_for_missing_files(): void
    {
        $this->useDbDisk();

        $this->get('/storage/photos/nope.png')->assertNotFound();
    }

    public function test_storage_route_refuses_path_traversal(): void
    {
        $this->useDbDisk();

        $this->get('/storage/..%2F..%2F.env')->assertNotFound();
        $this->get('/storage/photos/../../.env')->assertNotFound();
    }

    public function test_copy_and_delete_work_across_the_disk(): void
    {
        $this->useDbDisk();
        $disk = Storage::disk('public');
        $disk->put('photos/source.png', $bytes = base64_decode(self::PNG));

        $disk->copy('photos/source.png', 'originals/photos/source.png');
        $this->assertTrue($disk->exists('originals/photos/source.png'));
        $this->assertSame($bytes, $disk->get('originals/photos/source.png'));

        $disk->delete('originals/photos/source.png');
        $this->assertFalse($disk->exists('originals/photos/source.png'));
        $this->assertDatabaseMissing('file_storage', ['path' => 'originals/photos/source.png']);
    }
}
