<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FaqController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Faqs/Index', [
            'faqs'=>Faq::query()->orderBy('category')->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }
    public function store(Request $request)
    {
        Faq::create($this->validateData($request));
        return back()->with('success','FAQ created successfully.');
    }
    public function update(Request $request, Faq $faq)
    {
        $faq->update($this->validateData($request));
        return back()->with('success','FAQ updated successfully.');
    }
    public function toggle(Faq $faq)
    {
        $faq->update(['is_active'=>!$faq->is_active]);
        return back()->with('success','FAQ status updated.');
    }
    public function destroy(Faq $faq)
    {
        $faq->delete();
        return back()->with('success','FAQ deleted.');
    }
    private function validateData(Request $request): array
    {
        return $request->validate([
            'question'=>['required','string','max:1000'],
            'answer'=>['required','string'],
            'category'=>['nullable','string','max:100'],
            'is_active'=>['nullable','boolean'],
            'sort_order'=>['nullable','integer'],
        ]);
    }
}
