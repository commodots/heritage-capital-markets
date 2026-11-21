<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $role = DB::table('admin_roles')->where('name', 'Research Administrator')->first();

        if (! $role) {
            return;
        }

        $permissions = json_decode($role->permissions, true) ?: [];
        $permissions[] = 'articles';

        DB::table('admin_roles')
            ->where('id', $role->id)
            ->update([
                'permissions' => json_encode(array_values(array_unique($permissions))),
                'description' => 'Research reports, news articles, and newsletter access.',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        $role = DB::table('admin_roles')->where('name', 'Research Administrator')->first();

        if (! $role) {
            return;
        }

        $permissions = json_decode($role->permissions, true) ?: [];

        DB::table('admin_roles')
            ->where('id', $role->id)
            ->update([
                'permissions' => json_encode(array_values(array_diff($permissions, ['articles']))),
                'description' => 'Research and newsletter access.',
                'updated_at' => now(),
            ]);
    }
};
