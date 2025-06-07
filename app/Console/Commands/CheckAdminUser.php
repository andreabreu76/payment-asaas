<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckAdminUser extends Command
{
    protected $signature = 'check:admin';
    protected $description = 'Check if admin user exists in the database';

    public function handle()
    {
        $adminUser = User::where('email', 'admin@example.com')->first();

        if ($adminUser) {
            $this->info('Admin user exists in the database:');
            $this->info('ID: ' . $adminUser->id);
            $this->info('Name: ' . $adminUser->name);
            $this->info('Email: ' . $adminUser->email);
            $this->info('Created at: ' . $adminUser->created_at);
        } else {
            $this->error('Admin user does not exist in the database.');
        }
    }
}
