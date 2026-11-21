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
    public function index(Request $request)
    {
        $items = Media::query()->latest()->get();

        return Inertia::render('Admin/Media/Index', [
            'canManageDocuments' => (bool) $request->user()?->is_super_admin,
            'media' => $items
                ->filter(function (Media $media) {
                    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                    $extension = strtolower(pathinfo($media->file_path, PATHINFO_EXTENSION));

                    return in_array($media->mime_type, $allowedMimeTypes, true)
                        || in_array($extension, $allowedExtensions, true);
                })
                ->values(),
            'documents' => $items
                ->filter(fn (Media $media) => $media->isDownloadableDocument())
                ->values(),
        ]);
    }

    public function storeDocument(Request $request)
    {
        abort_unless($request->user()?->is_super_admin, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:1000'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $file = $request->file('file');
        $path = $file->store('downloads', 'public');

        Media::create([
            'name' => $validated['name'],
            'caption' => $validated['caption'] ?? null,
            'file_path' => $path,
            'disk' => 'public',
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return back()->with('success', 'Download document published successfully.');
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
        if ($media->isDownloadableDocument()) {
            abort_unless(auth()->user()?->is_super_admin, 403);
        }

        if ($media->file_path) {
            Storage::disk($media->disk ?: 'public')->delete($media->file_path);
        }
        $media->delete();

        return back()->with(
            'success',
            $media->isDownloadableDocument() ? 'Document deleted.' : 'Image deleted.',
        );
    }
}
