<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+6281444444444',
                'company_name' => 'John Company',
                'role' => 'user',
                'status' => 'active',
                'email_verified_at' => now(),
                'last_login_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+6281555555555',
                'company_name' => 'Smith Enterprises',
                'role' => 'user',
                'status' => 'active',
                'email_verified_at' => now(),
                'last_login_at' => now(),
            ],
            [
                'name' => 'Bob Wilson',
                'email' => 'bob.wilson@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+6281666666666',
                'company_name' => 'Wilson Corp',
                'role' => 'user',
                'status' => 'active',
                'email_verified_at' => now(),
                'last_login_at' => now(),
            ],
            [
                'name' => 'Alice Johnson',
                'email' => 'alice.johnson@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+6281777777777',
                'company_name' => 'Johnson Ltd',
                'role' => 'user',
                'status' => 'inactive',
                'email_verified_at' => now(),
                'last_login_at' => now()->subDays(30),
            ],
            [
                'name' => 'Charlie Brown',
                'email' => 'charlie.brown@example.com',
                'password' => Hash::make('password123'),
                'phone' => '+6281888888888',
                'company_name' => 'Brown Studio',
                'role' => 'user',
                'status' => 'suspended',
                'email_verified_at' => now(),
                'last_login_at' => now()->subDays(60),
            ],
        ];

        $createdCount = 0;

        foreach ($users as $user) {
            $exists = User::where('email', $user['email'])->exists();

            if (!$exists) {
                User::create($user);
                $createdCount++;
                $this->command->info("✓ User {$user['email']} created successfully!");
            } else {
                $this->command->warn("⚠ User {$user['email']} already exists!");
            }
        }

        if ($createdCount > 0) {
            $this->command->info('═══════════════════════════════════════════════════');
            $this->command->info("SUCCESS: {$createdCount} regular users created!");
            $this->command->info('═══════════════════════════════════════════════════');
            $this->command->info('Default login credentials for test users:');
            $this->command->info('Email: any user email above');
            $this->command->info('Password: password123');
            $this->command->info('═══════════════════════════════════════════════════');
        }
    }
}
