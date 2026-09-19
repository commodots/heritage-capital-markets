<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PageController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Pages/Index', [
            'pages' => Page::query()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:pages,slug'],
            'eyebrow' => ['nullable', 'string', 'max:100'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'featured_image' => ['nullable', 'image', 'max:5120'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request
                ->file('featured_image')
                ->store('pages', 'public');
        }

        $validated['is_published'] = $request->boolean('is_published');

        Page::create($validated);

        return back()->with(
            'success',
            'Page created successfully.'
        );
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'unique:pages,slug,' . $page->id,
            ],
            'eyebrow' => ['nullable', 'string', 'max:100'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'featured_image' => ['nullable', 'image', 'max:5120'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('featured_image')) {
            if (
                $page->featured_image &&
                Storage::disk('public')->exists($page->featured_image)
            ) {
                Storage::disk('public')->delete($page->featured_image);
            }

            $validated['featured_image'] = $request
                ->file('featured_image')
                ->store('pages', 'public');
        }

        $validated['is_published'] = $request->boolean('is_published');

        $page->update($validated);

        return back()->with(
            'success',
            'Page updated successfully.'
        );
    }

    public function toggle(Page $page)
    {
        $page->update([
            'is_published' => !$page->is_published,
        ]);

        return back()->with(
            'success',
            'Page publication status updated.'
        );
    }

    public function destroy(Page $page)
    {
        if (
            $page->featured_image &&
            Storage::disk('public')->exists($page->featured_image)
        ) {
            Storage::disk('public')->delete($page->featured_image);
        }

        $page->delete();

        return back()->with(
            'success',
            'Page deleted successfully.'
        );
    }


    private function validatePage(Request $request): array
    {
        return $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
            ],

            'eyebrow' => [
                'nullable',
                'string',
                'max:255',
            ],

            'excerpt' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'content' => [
                'nullable',
                'string',
            ],

            'featured_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'meta_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'meta_description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'is_published' => [
                'boolean',
            ],

            'sort_order' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);
    }
}