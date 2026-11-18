<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use App\Support\AdminFeatures;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AdminRoleController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/UserTypes/Index', [
            'roles' => AdminRole::withCount('users')->latest()->get(),
            'features' => AdminFeatures::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:admin_roles,name'],
            'description' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(AdminFeatures::keys())],
        ]);

        AdminRole::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'permissions' => array_values(array_unique($data['permissions'] ?? [])),
            'is_active' => true,
        ]);

        return back()->with('success', 'User type created successfully.');
    }

    public function update(Request $request, AdminRole $adminRole)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('admin_roles', 'name')->ignore($adminRole->id),
            ],
            'description' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(AdminFeatures::keys())],
            'is_active' => ['required', 'boolean'],
        ]);

        $adminRole->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'permissions' => array_values(array_unique($data['permissions'] ?? [])),
            'is_active' => $data['is_active'],
        ]);

        return back()->with('success', 'User type updated successfully.');
    }
}
