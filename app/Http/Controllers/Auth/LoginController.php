<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function create() {
        return Inertia::render('Auth/Login');
    }

    public function store(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'The provided credentials are incorrect.',
                'password' => 'The provided credentials are incorrect.',
            ]);
        }

        if (! $request->user()->is_admin) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'You do not have administrator access.',
                'password' => 'You do not have administrator access.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended('/admin');
    }

    public function destroy(Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
