<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MediaController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Media/Index', [
            'media'=>Media::query()->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data=$request->validate([
            'file'=>['required','file','max:10240','mimes:jpg,jpeg,png,webp,gif,svg,pdf,doc,docx,xls,xlsx'],
            'title'=>['nullable','string','max:255'],
        ]);
        $path=$request->file('file')->store('media','public');
        Media::create([
            'title'=>$data['title'] ?? $request->file('file')->getClientOriginalName(),
            'file_path'=>$path,
        ]);
        return back()->with('success','Media uploaded successfully.');
    }

    public function destroy(Media $media)
    {
        if ($media->file_path) Storage::disk('public')->delete($media->file_path);
        $media->delete();
        return back()->with('success','Media deleted.');
    }
}
