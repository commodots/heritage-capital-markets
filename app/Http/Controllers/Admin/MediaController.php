<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MediaController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Media/Index', [
            'media' => Media::query()
                ->latest()
                ->get()
                ->filter(function (Media $media) {
                    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                    $extension = strtolower(pathinfo($media->file_path, PATHINFO_EXTENSION));

                    return in_array($media->mime_type, $allowedMimeTypes, true)
                        || in_array($extension, $allowedExtensions, true);
                })
                ->values(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $file = $request->file('file');
        $path = $file->store('media', 'public');

        Media::create([
            'name' => Str::limit($file->getClientOriginalName(), 255, ''),
            'file_path' => $path,
            'disk' => 'public',
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return back()->with('success', 'Image uploaded successfully.');
    }

    public function destroy(Media $media)
    {
        if ($media->file_path) {
            Storage::disk($media->disk ?: 'public')->delete($media->file_path);
        }
        $media->delete();

        return back()->with('success', 'Image deleted.');
    }
}
