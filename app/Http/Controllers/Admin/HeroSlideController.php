<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class HeroSlideController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Homepage/HeroSlides/Index', [
            'slides' => HeroSlide::query()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'eyebrow' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'primary_label' => ['nullable', 'string', 'max:100'],
            'primary_url' => ['nullable', 'string', 'max:500'],
            'secondary_label' => ['nullable', 'string', 'max:100'],
            'secondary_url' => ['nullable', 'string', 'max:500'],
            'location_label' => ['nullable', 'string', 'max:100'],
            'location_text' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:1'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = '/storage/' . $request->file('image')->store('hero-slides', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        HeroSlide::create($validated);

        return back()->with('success', 'Hero slide created successfully.');
    }

    public function update(Request $request, HeroSlide $heroSlide)
    {
        $validated = $request->validate([
            'eyebrow' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'primary_label' => ['nullable', 'string', 'max:100'],
            'primary_url' => ['nullable', 'string', 'max:500'],
            'secondary_label' => ['nullable', 'string', 'max:100'],
            'secondary_url' => ['nullable', 'string', 'max:500'],
            'location_label' => ['nullable', 'string', 'max:100'],
            'location_text' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:1'],
        ]);

        if ($request->hasFile('image')) {
            $oldImage = $heroSlide->image;

            if ($oldImage && str_starts_with($oldImage, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $oldImage));
            }

            $validated['image'] = '/storage/' . $request->file('image')->store('hero-slides', 'public');
        } else {
            unset($validated['image']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        $heroSlide->update($validated);

        return back()->with('success', 'Hero slide updated successfully.');
    }

    public function destroy(HeroSlide $heroSlide)
    {
        if ($heroSlide->image && str_starts_with($heroSlide->image, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $heroSlide->image));
        }

        $heroSlide->delete();

        return back()->with('success', 'Hero slide deleted successfully.');
    }

    public function toggle(HeroSlide $heroSlide)
    {
        $heroSlide->update([
            'is_active' => ! $heroSlide->is_active,
        ]);

        return back()->with('success', $heroSlide->is_active
            ? 'Hero slide activated.'
            : 'Hero slide deactivated.');
    }
}
