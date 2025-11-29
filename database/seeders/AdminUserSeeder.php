<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@gmail.com', // Diperbaiki dari 'superadmin@example.com'
                'password' => Hash::make('superadmin123'),
                'phone' => '+6281111111111',
                'company_name' => 'Super Admin Company',
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'last_login_at' => now(),
            ],
            [
                'name' => 'Website Manager',
                'email' => 'webmanager@gmail.com', // Diperbaiki dari 'webmanager@example.com'
                'password' => Hash::make('webmanager123'),
                'phone' => '+6281222222222',
                'company_name' => 'Web Management Inc',
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'last_login_at' => now(),
            ],
            [
                'name' => 'System Operator',
                'email' => 'sysop@gmail.com', // Diperbaiki dari 'sysop@example.com'
                'password' => Hash::make('sysop12345'),
                'phone' => '+6281333333333',
                'company_name' => 'System Operations',
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'last_login_at' => now(),
            ],
        ];

        $createdCount = 0;

        foreach ($admins as $admin) {
            $exists = User::where('email', $admin['email'])->exists();

            if (!$exists) {
                User::create($admin);
                $createdCount++;
                $this->command->info("✓ Admin user {$admin['email']} created successfully!");
            } else {
                $this->command->warn("⚠ Admin user {$admin['email']} already exists!");
            }
        }

        if ($createdCount > 0) {
            $this->command->info('═══════════════════════════════════════════════════');
            $this->command->info("SUCCESS: {$createdCount} admin users created!");
            $this->command->info('═══════════════════════════════════════════════════');
            $this->command->info('Default login credentials for created accounts:');
            $this->command->info('1. superadmin@gmail.com / superadmin123');
            $this->command->info('2. webmanager@gmail.com / webmanager123');
            $this->command->info('3. sysop@gmail.com / sysop12345');
            $this->command->info('═══════════════════════════════════════════════════');
        } else {
            $this->command->info('═══════════════════════════════════════════════════');
            $this->command->info("INFO: All admin users already exist!");
            $this->command->info('═══════════════════════════════════════════════════');
        }
    }
}
