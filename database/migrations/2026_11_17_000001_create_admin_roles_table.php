<?php

use App\Support\AdminFeatures;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('admin_role_id')
                ->nullable()
                ->constrained('admin_roles')
                ->nullOnDelete();
            $table->boolean('is_super_admin')->default(false);
        });

        $now = now();
        $legacyRoleId = DB::table('admin_roles')->insertGetId([
            'name' => 'Legacy Administrator',
            'description' => 'Preserves the existing administrative modules for administrators present before role-based access was introduced.',
            'permissions' => json_encode(array_values(array_diff(
                AdminFeatures::keys(),
                ['user_management']
            ))),
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('users')
            ->where('is_admin', true)
            ->update(['admin_role_id' => $legacyRoleId]);

        foreach ([
            [
                'Research Administrator',
                'Research and newsletter access.',
                ['dashboard', 'research', 'newsletter'],
            ],
            [
                'Content Administrator',
                'Website content, publishing, and media access.',
                ['dashboard', 'pages', 'board_management', 'articles', 'events', 'faqs', 'media'],
            ],
        ] as [$name, $description, $permissions]) {
            DB::table('admin_roles')->insert([
                'name' => $name,
                'description' => $description,
                'permissions' => json_encode($permissions),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['admin_role_id']);
            $table->dropColumn(['admin_role_id', 'is_super_admin']);
        });

        Schema::dropIfExists('admin_roles');
    }
};
