<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AdminUserController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/AdminUsers/Index', [
            'users' => User::with('adminRole')
                ->where(function ($query) {
                    $query->where('is_admin', true)
                        ->orWhereNotNull('admin_role_id');
                })
                ->latest()
                ->get(),
            'roles' => AdminRole::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:10', 'max:255'],
            'admin_role_id' => [
                'required',
                Rule::exists('admin_roles', 'id')->where('is_active', true),
            ],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'admin_role_id' => $data['admin_role_id'],
            'is_admin' => true,
            'is_super_admin' => false,
        ]);

        return back()->with('success', 'Administrator created successfully.');
    }

    public function update(Request $request, User $user)
    {
        abort_unless(
            ($user->is_admin || $user->admin_role_id !== null) && ! $user->is_super_admin,
            403
        );

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:10', 'max:255'],
            'admin_role_id' => [
                'required',
                Rule::exists('admin_roles', 'id')->where('is_active', true),
            ],
        ]);

        $updates = Arr::except($data, 'password');
        if (! empty($data['password'])) {
            $updates['password'] = Hash::make($data['password']);
        }

        $user->update($updates);

        return back()->with('success', 'Administrator updated successfully.');
    }

    public function toggle(Request $request, User $user)
    {
        abort_unless($user->is_admin || $user->admin_role_id !== null, 404);
        abort_if($user->is_super_admin || $user->is($request->user()), 403);

        $user->update(['is_admin' => ! $user->is_admin]);

        return back()->with('success', 'Administrator access updated.');
    }
}
