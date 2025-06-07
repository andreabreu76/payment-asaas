<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class EnsureAdminUserExists extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if admin user exists
        $adminUser = User::where('email', 'admin@example.com')->first();

        // If admin user doesn't exist, create it
        if (!$adminUser) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin'),
                'email_verified_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Do nothing in down migration
    }
}
