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
        'privacy' => 'PrivacyPolicy',
        'terms' => 'TermsOfUse',
        'risk' => 'RiskDisclosure',
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
    
    
    $incomingKeys = array_keys($request->all());
    $allRules = PublicPageContent::validationRules($group);
    $rules = collect($allRules)->only($incomingKeys)->toArray();

    $validated = $request->validate($rules);
    $currentValues = PublicPageContent::groupValues($group);

    $replacedImages = [];

    
    foreach ($validated as $key => $value) {
        $field = $pageGroup['fields'][$key] ?? null;
        
        if ($field && ($field[3] ?? null) === 'image') {
            if ($request->hasFile($key)) {
                $validated[$key] = $request->file($key)->store("public-page-content/{$group}", 'public');
                if ($currentValues[$key] ?? null) {
                    $replacedImages[] = $currentValues[$key];
                }
            } else {
                
                $validated[$key] = $currentValues[$key] ?? null;
            }
        }
    }

    foreach ($validated as $key => $value) {
        if (array_key_exists($key, $pageGroup['fields'])) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }
    }

    foreach (array_unique($replacedImages) as $image) {
        if (Storage::disk('public')->exists($image)) {
            Storage::disk('public')->delete($image);
        }
    }

    return back()->with('success', 'Public page content updated successfully.');
}
}
