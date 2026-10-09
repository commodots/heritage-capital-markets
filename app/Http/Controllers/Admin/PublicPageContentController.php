<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\PublicPageContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PublicPageContentController extends Controller
{
    private const COMPONENTS = [
        'board' => 'BoardManagement',
        'governance' => 'CorporateGovernance',
        'fees' => 'FeesCharges',
        'resources' => 'Resources',
    ];

    public function edit(string $group)
    {
        $pageGroup = PublicPageContent::group($group);

        return Inertia::render('Admin/PublicPageContent/' . self::COMPONENTS[$group], [
            'groupKey' => $group,
            'group' => $pageGroup,
            'settings' => PublicPageContent::groupValues($group),
        ]);
    }

    public function update(Request $request, string $group)
    {
        $pageGroup = PublicPageContent::group($group);
        $validated = $request->validate(PublicPageContent::validationRules($group));
        $currentValues = PublicPageContent::groupValues($group);

        $replacedImages = [];
        foreach ($pageGroup['fields'] as $key => $field) {
            if (($field[3] ?? null) !== 'image' || ! $request->hasFile($key)) {
                continue;
            }

            $validated[$key] = $request->file($key)->store("public-page-content/{$group}", 'public');
            if ($currentValues[$key] ?? null) {
                $replacedImages[] = $currentValues[$key];
            }
        }

        foreach ($pageGroup['fields'] as $key => $field) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $validated[$key] ?? $currentValues[$key]]
            );
        }

        foreach (array_unique($replacedImages) as $image) {
            if (Storage::disk('public')->exists($image)) {
                Storage::disk('public')->delete($image);
            }
        }

        return back()->with('success', 'Public page content updated successfully.');
    }
}
