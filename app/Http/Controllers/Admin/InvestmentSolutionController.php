<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvestmentSolution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class InvestmentSolutionController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/InvestmentSolutions/Index', [
            'solutions' => InvestmentSolution::query()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'featured_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'icon' => ['nullable', 'string', 'max:100'],
            'button_label' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:1'],
        ]);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] =
                '/storage/' .
                $request->file('featured_image')->store(
                    'investment-solutions',
                    'public'
                );
        }

        $validated['is_active'] = $request->boolean('is_active');

        InvestmentSolution::create($validated);

        return back()->with(
            'success',
            'Investment solution created successfully.'
        );
    }

    public function update(
        Request $request,
        InvestmentSolution $investmentSolution
    ) {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'featured_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'icon' => ['nullable', 'string', 'max:100'],
            'button_label' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:1'],
        ]);

        if ($request->hasFile('featured_image')) {
            $oldImage = $investmentSolution->featured_image;

            if (
                $oldImage &&
                str_starts_with($oldImage, '/storage/')
            ) {
                Storage::disk('public')->delete(
                    str_replace('/storage/', '', $oldImage)
                );
            }

            $validated['featured_image'] =
                '/storage/' .
                $request->file('featured_image')->store(
                    'investment-solutions',
                    'public'
                );
        } else {
            unset($validated['featured_image']);
        }

        $validated['is_active'] = $request->boolean('is_active');

        $investmentSolution->update($validated);

        return back()->with(
            'success',
            'Investment solution updated successfully.'
        );
    }

    public function destroy(
        InvestmentSolution $investmentSolution
    ) {
        if (
            $investmentSolution->featured_image &&
            str_starts_with(
                $investmentSolution->featured_image,
                '/storage/'
            )
        ) {
            Storage::disk('public')->delete(
                str_replace(
                    '/storage/',
                    '',
                    $investmentSolution->featured_image
                )
            );
        }

        $investmentSolution->delete();

        return back()->with(
            'success',
            'Investment solution deleted successfully.'
        );
    }

    public function toggle(
        InvestmentSolution $investmentSolution
    ) {
        $investmentSolution->update([
            'is_active' => ! $investmentSolution->is_active,
        ]);

        return back()->with(
            'success',
            $investmentSolution->is_active
                ? 'Investment solution activated.'
                : 'Investment solution deactivated.'
        );
    }
}