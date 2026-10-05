<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroImage;
use App\Models\IntroImage;
use App\Models\Photo;
use App\Models\StoryPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Re-crop photos that are already uploaded, so the admin can fine-tune
 * exactly how each photo looks to guests (e.g. keep faces in frame).
 *
 * Uses PHP's GD extension (no extra packages). The first crop keeps a backup
 * of the original file (…/originals/…), so every later crop always works
 * from the full-quality source instead of re-cropping a cropped image.
 */
class ImageCropController extends Controller
{
    public function update(Request $request, string $type, int $id)
    {
        $request->validate([
            'x' => 'required|numeric|min:0',
            'y' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:1',
            'height' => 'required|numeric|min:1',
        ]);

        $record = $this->resolve($type, $id);
        if (!$record) {
            abort(404);
        }

        $disk = Storage::disk('public');
        $path = $record->file_path ?? $record->image_path;

        if (!$path || !$disk->exists($path)) {
            return back()->with('error', 'Photo file could not be found.');
        }

        // Keep a pristine copy of the original once; later crops use it as source
        $originalPath = 'originals/' . $path;
        $sourcePath = $disk->exists($originalPath) ? $originalPath : $path;
        if (!$disk->exists($originalPath)) {
            $disk->copy($path, $originalPath);
        }

        // Read through the disk so this works for local files and object storage alike
        $bytes = $disk->get($sourcePath);
        $info = $bytes ? @getimagesizefromstring($bytes) : false;
        if (!$info) {
            return back()->with('error', 'That file could not be read as an image.');
        }

        [$w, $h] = $info;
        $mime = $info['mime'];

        $src = in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true)
            ? @imagecreatefromstring($bytes)
            : null;

        if (!$src) {
            return back()->with('error', 'This image format could not be processed.');
        }

        // Clamp the crop box to the actual image bounds
        $cx = max(0, min((int) round($request->input('x')), $w - 1));
        $cy = max(0, min((int) round($request->input('y')), $h - 1));
        $cw = max(1, min((int) round($request->input('width')), $w - $cx));
        $ch = max(1, min((int) round($request->input('height')), $h - $cy));

        // Never let a stored photo balloon the page: cap the longest side at 1800px
        $maxSide = 1800;
        $scale = min(1, $maxSide / max($cw, $ch));
        $dw = max(1, (int) round($cw * $scale));
        $dh = max(1, (int) round($ch * $scale));

        $dst = imagecreatetruecolor($dw, $dh);
        imagecopyresampled($dst, $src, 0, 0, $cx, $cy, $dw, $dh, $cw, $ch);

        ob_start();
        imagejpeg($dst, null, 88);
        $data = ob_get_clean();

        imagedestroy($src);
        imagedestroy($dst);

        $disk->put($path, $data);

        $labels = [
            'story' => 'Story photo', 'hero' => 'Hero photo',
            'intro' => 'Popup photo', 'gallery' => 'Guest photo',
        ];

        return back()->with('success', ($labels[$type] ?? 'Photo') . ' cropped — it now shows exactly how you framed it.');
    }

    protected function resolve(string $type, int $id): ?object
    {
        return match ($type) {
            'story' => StoryPhoto::find($id),
            'hero' => HeroImage::find($id),
            'intro' => IntroImage::find($id),
            'gallery' => Photo::find($id),
            default => null,
        };
    }
}
