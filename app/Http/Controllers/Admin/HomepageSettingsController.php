<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomepageSettingsController extends Controller
{
    /**
     * Homepage settings that are currently editable.
     */
    private function settingKeys(): array
    {
        return [
            'homepage_why_eyebrow',
            'homepage_why_title',
            'homepage_why_description',

            'homepage_stat_1_value',
            'homepage_stat_1_label',

            'homepage_stat_2_value',
            'homepage_stat_2_label',

            'homepage_stat_3_value',
            'homepage_stat_3_label',

            'homepage_stat_4_value',
            'homepage_stat_4_label',

            'homepage_about_eyebrow',
            'homepage_about_title',
            'homepage_about_text_1',
            'homepage_about_text_2',
            'homepage_about_button',
        ];
    }

    /**
     * Display homepage settings.
     */
    public function index()
    {
        $settings = SiteSetting::query()
            ->whereIn('key', $this->settingKeys())
            ->pluck('value', 'key');

        return Inertia::render('Admin/Homepage/Settings', [
            'settings' => $settings,
        ]);
    }

    /**
     * Save homepage settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'homepage_why_eyebrow' => ['nullable', 'string', 'max:100'],
            'homepage_why_title' => ['nullable', 'string', 'max:255'],
            'homepage_why_description' => ['nullable', 'string', 'max:2000'],

            'homepage_stat_1_value' => ['nullable', 'string', 'max:50'],
            'homepage_stat_1_label' => ['nullable', 'string', 'max:100'],

            'homepage_stat_2_value' => ['nullable', 'string', 'max:50'],
            'homepage_stat_2_label' => ['nullable', 'string', 'max:100'],

            'homepage_stat_3_value' => ['nullable', 'string', 'max:50'],
            'homepage_stat_3_label' => ['nullable', 'string', 'max:100'],

            'homepage_stat_4_value' => ['nullable', 'string', 'max:50'],
            'homepage_stat_4_label' => ['nullable', 'string', 'max:100'],

            'homepage_about_eyebrow' => ['nullable', 'string', 'max:100'],
            'homepage_about_title' => ['nullable', 'string', 'max:255'],
            'homepage_about_text_1' => ['nullable', 'string', 'max:3000'],
            'homepage_about_text_2' => ['nullable', 'string', 'max:3000'],
            'homepage_about_button' => ['nullable', 'string', 'max:100'],
        ]);

        foreach ($validated as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with(
            'success',
            'Homepage settings updated successfully.'
        );
    }
}