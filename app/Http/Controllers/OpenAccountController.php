<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OpenAccountController extends Controller
{
    public function index()
    {
        return Inertia::render('OpenAccount');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:50'],
            'account_type' => ['required', 'string', 'max:100'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        $accountType = $validated['account_type'];
        unset($validated['account_type']);

        Enquiry::create([
            ...$validated,
            'subject' => 'Open an Account Request - ' . $accountType,
            'source' => 'open-account',
            'status' => 'new',
        ]);

        return back()->with(
            'success',
            'Thank you. Your account request has been received. Our team will contact you with the next steps.'
        );
    }
}
