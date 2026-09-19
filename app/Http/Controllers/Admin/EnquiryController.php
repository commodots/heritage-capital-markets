<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EnquiryController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Enquiries/Index', [
            'enquiries' => Enquiry::query()
                ->latest()
                ->get(),
        ]);
    }

    public function show(Enquiry $enquiry)
    {
        return Inertia::render('Admin/Enquiries/Show', [
            'enquiry' => $enquiry,
        ]);
    }

    public function update(Request $request, Enquiry $enquiry)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:new,in_progress,closed',
            ],
        ]);

        $enquiry->update($validated);

        return back()->with('success', 'Enquiry status updated.');
    }
}