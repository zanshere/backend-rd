<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('packages')->insert([
            [
                'name' => 'Website Sederhana',
                'description' => 'Paket website yang ccocok untuk usaha kecil dan personal',
                'base_price' => 1000000,
                'is_custom_price' => false,
                'features' => json_encode(['Responsive Design', '1-5 Halaman', 'Contact Form', 'SEO Basic', 'Hosting dan Domain 1 Tahun', 'Support 1 Bulan']),
                'estimated_days' => 14,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Website Bisnis',
                'description' => 'Paket website yang ideal untuk bisnis yang sedang berkembang',
                'base_price' => 2500000,
                'is_custom_price' => false,
                'features' => json_encode(['All Fitur Sederhana' ,'Responsive Design', '5-15 Halaman', 'Integrasi Media Social', 'Blog Section', 'Google Analytics', 'Optimasi SEO Lanjutan', 'Support 3 Bulan', 'Free Training Penggunaan']),
                'estimated_days' => 21,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Website Premium',
                'description' => 'Solusi lengkap untuk perusahaan',
                'base_price' => 5000000,
                'is_custom_price' => false,
                'features' => json_encode(['All Fitur Web Bisnis', 'Sistem Admin Custom', 'Halaman Unlimited', 'Integrasi Payment Gateway', 'Integrasi Custom API', 'Database Management', 'Backup Rutin', 'Priority Support 6 Bulan', 'Garansi 1 Tahun']),
                'estimated_days' => 30,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'E-commerce',
                'description' => 'Platform toko online lengkap',
                'base_price' => 0,
                'is_custom_price' => true,
                'features' => json_encode(['Website Toko Online Lengkap', 'System Inventory Management', 'Multiple payment Methods', 'Shipping Integration', 'Customer Account System', 'Product Review & Ratings', 'Sales Analytics Dashboard', 'Mobile App Ready', 'Priority Support 12 Bulan']),
                'estimated_days' => 45,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
