<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        if (Schema::hasTable('admin_roles') && ! DB::table('admin_roles')->where('name', 'Chat Support')->exists()) {
            DB::table('admin_roles')->insert([
                'name' => 'Chat Support',
                'description' => 'Dashboard, live chat, and enquiry access.',
                'permissions' => json_encode(['dashboard', 'live_chat', 'enquiries']),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (Schema::hasTable('site_settings')) {
            foreach ([
                'chat_enabled' => '1',
                'chat_working_days' => json_encode(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
                'chat_open_time' => '08:00',
                'chat_close_time' => '17:00',
                'chat_timezone' => 'Africa/Lagos',
                'chat_offline_message' => 'Our office is currently closed. Please leave an enquiry and our team will respond.',
            ] as $key => $value) {
                DB::table('site_settings')->updateOrInsert(['key' => $key], [
                    'value' => $value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('admin_roles')) {
            DB::table('admin_roles')->where('name', 'Chat Support')->delete();
        }
    }
};
