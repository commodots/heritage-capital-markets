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
        $keys = [
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

            'homepage_solutions_eyebrow',
            'homepage_solutions_title_line_1',
            'homepage_solutions_title_line_2',
            'homepage_solutions_description',
            'homepage_solutions_button',
            'homepage_market_eyebrow',
            'homepage_market_title',
            'homepage_research_eyebrow',
            'homepage_research_title_line_1',
            'homepage_research_title_line_2',
            'homepage_research_button',
        ];

        for ($number = 1; $number <= 4; $number++) {
            $keys[] = "homepage_benefit_{$number}_title";
            $keys[] = "homepage_benefit_{$number}_text";
            $keys[] = "homepage_highlight_{$number}_title";
            $keys[] = "homepage_highlight_{$number}_text";
        }

        for ($number = 1; $number <= 3; $number++) {
            $keys[] = "homepage_research_{$number}_category";
            $keys[] = "homepage_research_{$number}_title";
            $keys[] = "homepage_research_{$number}_text";
        }

        return $keys;
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
        $rules = [
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
            'homepage_solutions_eyebrow' => ['nullable', 'string', 'max:100'],
            'homepage_solutions_title_line_1' => ['nullable', 'string', 'max:255'],
            'homepage_solutions_title_line_2' => ['nullable', 'string', 'max:255'],
            'homepage_solutions_description' => ['nullable', 'string', 'max:2000'],
            'homepage_solutions_button' => ['nullable', 'string', 'max:100'],
            'homepage_market_eyebrow' => ['nullable', 'string', 'max:100'],
            'homepage_market_title' => ['nullable', 'string', 'max:255'],
            'homepage_research_eyebrow' => ['nullable', 'string', 'max:100'],
            'homepage_research_title_line_1' => ['nullable', 'string', 'max:255'],
            'homepage_research_title_line_2' => ['nullable', 'string', 'max:255'],
            'homepage_research_button' => ['nullable', 'string', 'max:100'],
        ];

        for ($number = 1; $number <= 4; $number++) {
            $rules["homepage_benefit_{$number}_title"] = ['nullable', 'string', 'max:255'];
            $rules["homepage_benefit_{$number}_text"] = ['nullable', 'string', 'max:1000'];
            $rules["homepage_highlight_{$number}_title"] = ['nullable', 'string', 'max:255'];
            $rules["homepage_highlight_{$number}_text"] = ['nullable', 'string', 'max:1000'];
        }

        for ($number = 1; $number <= 3; $number++) {
            $rules["homepage_research_{$number}_category"] = ['nullable', 'string', 'max:100'];
            $rules["homepage_research_{$number}_title"] = ['nullable', 'string', 'max:255'];
            $rules["homepage_research_{$number}_text"] = ['nullable', 'string', 'max:1000'];
        }

        $validated = $request->validate($rules);

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