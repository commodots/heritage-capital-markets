<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResearchReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ResearchReportController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/ResearchReports/Index', [
            'reports' => ResearchReport::query()->orderByDesc('published_at')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if (blank($data['slug'] ?? null)) {
            $data['slug'] = $this->uniqueSlug($data['title']);
        }

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('research-reports', 'public');
        }
        if ($request->hasFile('pdf_file')) {
            $data['pdf_file'] = $request->file('pdf_file')->store('research-reports', 'public');
        }

        ResearchReport::create($data);
        return back()->with('success', 'Research report created successfully.');
    }

    public function update(Request $request, ResearchReport $researchReport)
    {
        $data = $this->validateData($request, $researchReport);

        if (blank($data['slug'] ?? null)) {
            $data['slug'] = $this->uniqueSlug($data['title'], $researchReport);
        }

        if ($request->hasFile('featured_image')) {
            if ($researchReport->featured_image) Storage::disk('public')->delete($researchReport->featured_image);
            $data['featured_image'] = $request->file('featured_image')->store('research-reports', 'public');
        }
        if ($request->hasFile('pdf_file')) {
            if ($researchReport->pdf_file) Storage::disk('public')->delete($researchReport->pdf_file);
            $data['pdf_file'] = $request->file('pdf_file')->store('research-reports', 'public');
        }

        $researchReport->update($data);
        return back()->with('success', 'Research report updated successfully.');
    }

    public function toggle(ResearchReport $researchReport)
    {
        $researchReport->update(['is_published' => ! $researchReport->is_published]);
        return back()->with('success', 'Research report status updated.');
    }

    public function destroy(ResearchReport $researchReport)
    {
        if ($researchReport->featured_image) Storage::disk('public')->delete($researchReport->featured_image);
        if ($researchReport->pdf_file) Storage::disk('public')->delete($researchReport->pdf_file);
        $researchReport->delete();
        return back()->with('success', 'Research report deleted.');
    }

    private function validateData(Request $request, ?ResearchReport $researchReport = null): array
    {
        return $request->validate([
            'title' => ['required','string','max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('research_reports', 'slug')->ignore($researchReport?->id),
            ],
            'category' => ['nullable','string','max:255'],
            'excerpt' => ['nullable','string','max:2000'],
            'content' => ['nullable','string'],
            'featured_image' => ['nullable','image','max:5120'],
            'pdf_file' => ['nullable','file','mimes:pdf','max:20480'],
            'published_at' => ['nullable','date'],
            'is_published' => ['nullable','boolean'],
            'sort_order' => ['nullable','integer'],
        ]);
    }

    private function uniqueSlug(string $title, ?ResearchReport $researchReport = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $suffix = 2;

        while (
            ResearchReport::query()
                ->where('slug', $slug)
                ->when($researchReport, fn ($query) => $query->where('id', '!=', $researchReport->id))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $suffix++;
        }

        return $slug;
    }
}
