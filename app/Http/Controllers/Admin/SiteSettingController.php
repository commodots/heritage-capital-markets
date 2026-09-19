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
        return Inertia::render('Admin/Settings/Index', [
            'settings'=>SiteSetting::query()->pluck('value','key'),
        ]);
    }

    public function update(Request $request)
    {
        $data=$request->validate([
            'company_name'=>['nullable','string','max:255'],
            'phone'=>['nullable','string','max:100'],
            'email'=>['nullable','email','max:255'],
            'address'=>['nullable','string','max:1000'],
            'facebook'=>['nullable','string','max:500'],
            'linkedin'=>['nullable','string','max:500'],
            'instagram'=>['nullable','string','max:500'],
            'footer_text'=>['nullable','string','max:1000'],
        ]);

        foreach($data as $key=>$value) SiteSetting::updateOrCreate(['key'=>$key],['value'=>$value]);
        return back()->with('success','Site settings updated successfully.');
    }
}
