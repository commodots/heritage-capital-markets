<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admin_roles')) {
            return;
        }

        $role = DB::table('admin_roles')->where('name', 'Chat Support')->first();
        if (! $role) {
            return;
        }

        $permissions = json_decode($role->permissions ?? '[]', true);
        $permissions = is_array($permissions)
            ? array_values(array_filter($permissions, fn ($permission) => $permission !== 'live_chat'))
            : [];
        $newName = DB::table('admin_roles')->where('name', 'Enquiry Support')->exists()
            ? 'Enquiry Support (Legacy)'
            : 'Enquiry Support';

        DB::table('admin_roles')->where('id', $role->id)->update([
            'name' => $newName,
            'description' => 'Dashboard and enquiry access.',
            'permissions' => json_encode($permissions),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('admin_roles')) {
            return;
        }

        $role = DB::table('admin_roles')
            ->whereIn('name', ['Enquiry Support', 'Enquiry Support (Legacy)'])
            ->first();
        if (! $role || DB::table('admin_roles')->where('name', 'Chat Support')->exists()) {
            return;
        }

        $permissions = json_decode($role->permissions ?? '[]', true);
        $permissions = is_array($permissions) ? $permissions : [];
        $permissions[] = 'live_chat';

        DB::table('admin_roles')->where('id', $role->id)->update([
            'name' => 'Chat Support',
            'description' => 'Dashboard, live chat, and enquiry access.',
            'permissions' => json_encode(array_values(array_unique($permissions))),
            'updated_at' => now(),
        ]);
    }
};
