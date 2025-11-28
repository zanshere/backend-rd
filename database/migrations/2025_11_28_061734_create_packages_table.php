<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Usaha Kecil, Bisnis Menengah, Bisnis, E-commerce
            $table->text('description');
            $table->decimal('base_price', 12, 2); // Harga dasar
            $table->boolean('is_custom_price')->default(false); // Untuk e-commerce
            $table->json('features')->nullable(); // Fitur-fitur paket
            $table->integer('estimated_days')->nullable(); // Estimasi pengerjaan
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
