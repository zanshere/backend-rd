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
                'description' => 'Paket website yang cocok untuk usaha kecil dan personal',
                'type' => 'usaha_kecil',
                'base_price' => 1000000,
                'is_custom_price' => false,
                'features' => json_encode([
                    'Responsive Design',
                    '1-5 Halaman',
                    'Contact Form',
                    'SEO Basic',
                    'Hosting dan Domain 1 Tahun',
                    'Support 1 Bulan'
                ]),
                'delivery_time' => 14,
                'revision_limit' => 3,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Website Bisnis Menengah',
                'description' => 'Paket website yang ideal untuk bisnis yang sedang berkembang',
                'type' => 'bisnis_menengah',
                'base_price' => 2500000,
                'is_custom_price' => false,
                'features' => json_encode([
                    'All Fitur Sederhana',
                    'Responsive Design',
                    '5-15 Halaman',
                    'Integrasi Media Sosial',
                    'Blog Section',
                    'Google Analytics',
                    'Optimasi SEO Lanjutan',
                    'Support 3 Bulan',
                    'Free Training Penggunaan'
                ]),
                'delivery_time' => 21,
                'revision_limit' => 5,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Website Bisnis Premium',
                'description' => 'Solusi lengkap untuk perusahaan',
                'type' => 'bisnis',
                'base_price' => 5000000,
                'is_custom_price' => false,
                'features' => json_encode([
                    'All Fitur Web Bisnis',
                    'Sistem Admin Custom',
                    'Halaman Unlimited',
                    'Integrasi Payment Gateway',
                    'Integrasi Custom API',
                    'Database Management',
                    'Backup Rutin',
                    'Priority Support 6 Bulan',
                    'Garansi 1 Tahun'
                ]),
                'delivery_time' => 30,
                'revision_limit' => 7,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'E-commerce',
                'description' => 'Platform toko online lengkap',
                'type' => 'e_commerce',
                'base_price' => 0,
                'is_custom_price' => true,
                'features' => json_encode([
                    'Website Toko Online Lengkap',
                    'System Inventory Management',
                    'Multiple Payment Methods',
                    'Shipping Integration',
                    'Customer Account System',
                    'Product Review & Ratings',
                    'Sales Analytics Dashboard',
                    'Mobile App Ready',
                    'Priority Support 12 Bulan'
                ]),
                'delivery_time' => 45,
                'revision_limit' => 10,
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
