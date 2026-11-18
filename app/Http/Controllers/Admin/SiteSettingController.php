<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::query()->pluck('value', 'key')->all();
        $workingDays = json_decode($settings['chat_working_days'] ?? '', true);
        $settings['chat_working_days'] = is_array($workingDays)
            ? $workingDays
            : ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $data=$request->validate([
            'company_name'=>['nullable','string','max:255'],
            'phone'=>['nullable','string','max:100'],
            'email'=>['nullable','email','max:255'],
            'address'=>['nullable','string','max:1000'],
            'address_aba'=>['nullable','string','max:1000'],
            'facebook'=>['nullable','string','max:500'],
            'linkedin'=>['nullable','string','max:500'],
            'instagram'=>['nullable','string','max:500'],
            'footer_text'=>['nullable','string','max:1000'],
            'chat_enabled' => ['required', 'boolean'],
            'chat_working_days' => ['required', 'array', 'min:1'],
            'chat_working_days.*' => ['required', 'string', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'chat_open_time' => ['required', 'date_format:H:i'],
            'chat_close_time' => ['required', 'date_format:H:i', 'after:chat_open_time'],
            'chat_timezone' => ['required', 'timezone'],
            'chat_offline_message' => ['required', 'string', 'max:1000'],
            'chat_notification_email' => ['nullable', 'email', 'max:255'],
        ]);

        foreach ($data as $key => $value) {
            if ($key === 'chat_enabled') $value = $value ? '1' : '0';
            if ($key === 'chat_working_days') $value = json_encode(array_values(array_unique($value)));
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return back()->with('success','Site settings updated successfully.');
    }
}
