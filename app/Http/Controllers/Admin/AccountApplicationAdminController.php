<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AccountApplicationAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = AccountApplication::query()->latest();
        if ($request->filled('status')) $query->where('status', $request->string('status'));
        if ($request->filled('account_type')) $query->where('account_type', $request->string('account_type'));

        return Inertia::render('Admin/AccountApplications/Index', [
            'applications' => $query->paginate(20)->withQueryString(),
            'filters' => $request->only('status', 'account_type'),
        ]);
    }

    public function show(AccountApplication $application)
    {
        return Inertia::render('Admin/AccountApplications/Show', [
            'application' => $application,
            'documentNames' => array_keys($application->documents ?? []),
        ]);
    }

    public function update(Request $request, AccountApplication $application)
    {
        $data = $request->validate([
            'status' => ['required', 'in:submitted,under_review,more_information_required,approved,rejected,closed'],
            'review_notes' => ['nullable', 'string', 'max:5000'],
        ]);
        $application->update($data);
        return back()->with('success', 'Application updated.');
    }

    public function download(AccountApplication $application, string $document)
    {
        abort_unless(auth()->check() && auth()->user()->is_admin, 403);
        $path = ($application->documents ?? [])[$document] ?? null;
        abort_unless($path && Storage::disk('local')->exists($path), 404);
        return Storage::disk('local')->download($path, basename($path));
    }
}
