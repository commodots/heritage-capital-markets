<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketIndex;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarketIndexController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/MarketIndices/Index', [
            'indices' => MarketIndex::query()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'symbol' => ['nullable', 'string', 'max:100'],
            'value' => ['required', 'numeric'],
            'change' => ['required', 'numeric'],
            'change_percent' => ['required', 'numeric'],
            'is_positive' => ['required', 'boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:1'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_positive'] = $request->boolean('is_positive');

        MarketIndex::create($validated);

        return back()->with(
            'success',
            'Market index created successfully.'
        );
    }

    public function update(Request $request, MarketIndex $marketIndex)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'symbol' => ['nullable', 'string', 'max:100'],
            'value' => ['required', 'numeric'],
            'change' => ['required', 'numeric'],
            'change_percent' => ['required', 'numeric'],
            'is_positive' => ['required', 'boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:1'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_positive'] = $request->boolean('is_positive');

        $marketIndex->update($validated);

        return back()->with(
            'success',
            'Market index updated successfully.'
        );
    }

    public function destroy(MarketIndex $marketIndex)
    {
        $marketIndex->delete();

        return back()->with(
            'success',
            'Market index deleted successfully.'
        );
    }

    public function toggle(MarketIndex $marketIndex)
    {
        $marketIndex->update([
            'is_active' => ! $marketIndex->is_active,
        ]);

        return back()->with(
            'success',
            $marketIndex->is_active
                ? 'Market index activated.'
                : 'Market index deactivated.'
        );
    }
}