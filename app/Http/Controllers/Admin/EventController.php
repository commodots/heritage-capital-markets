<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Events/Index', ['events'=>Event::query()->orderByDesc('event_date')->orderBy('sort_order')->get()]);
    }
    public function store(Request $request)
    {
        $data=$this->validateData($request);
        if ($request->hasFile('featured_image')) $data['featured_image']=$request->file('featured_image')->store('events','public');
        $data['slug']=$data['slug'] ?? Str::slug($data['title']);
        Event::create($data);
        return back()->with('success','Event created successfully.');
    }
    public function update(Request $request, Event $event)
    {
        $data=$this->validateData($request);
        if ($request->hasFile('featured_image')) {
            if ($event->featured_image) Storage::disk('public')->delete($event->featured_image);
            $data['featured_image']=$request->file('featured_image')->store('events','public');
        }
        $data['slug']=$data['slug'] ?? Str::slug($data['title']);
        $event->update($data);
        return back()->with('success','Event updated successfully.');
    }
    public function toggle(Event $event)
    {
        $event->update(['is_published'=>!$event->is_published]);
        return back()->with('success','Event status updated.');
    }
    public function destroy(Event $event)
    {
        if ($event->featured_image) Storage::disk('public')->delete($event->featured_image);
        $event->delete();
        return back()->with('success','Event deleted.');
    }
    private function validateData(Request $request): array
    {
        return $request->validate([
            'title'=>['required','string','max:255'],'slug'=>['nullable','string','max:255'],
            'excerpt'=>['nullable','string','max:2000'],'description'=>['nullable','string'],
            'featured_image'=>['nullable','image','max:5120'],'event_date'=>['nullable','date'],
            'location'=>['nullable','string','max:255'],'is_published'=>['nullable','boolean'],
            'sort_order'=>['nullable','integer'],
        ]);
    }
}
