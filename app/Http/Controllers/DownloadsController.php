<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DownloadsController extends Controller
{
    public function index()
    {
        return Inertia::render('Resources/Downloads', [
            'documents' => Media::query()
                ->latest()
                ->get()
                ->filter(fn (Media $media) => $media->isDownloadableDocument())
                ->map(fn (Media $media) => [
                    'id' => $media->id,
                    'name' => $media->name,
                    'caption' => $media->caption,
                ])
                ->values(),
        ]);
    }

    public function download(Media $media)
    {
        abort_unless($media->isDownloadableDocument(), 404);
        abort_unless(Storage::disk($media->disk ?: 'public')->exists($media->file_path), 404);

        $fileName = Str::slug($media->name) ?: 'document';
        $extension = strtolower(pathinfo($media->file_path, PATHINFO_EXTENSION));

        return Storage::disk($media->disk ?: 'public')->download(
            $media->file_path,
            "{$fileName}.{$extension}",
        );
    }
}
