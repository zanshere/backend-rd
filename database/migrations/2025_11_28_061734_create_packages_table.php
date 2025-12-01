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
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['usaha_kecil', 'bisnis_menengah', 'bisnis', 'e_commerce']);
            $table->decimal('base_price', 15, 0)->default(0);
            $table->boolean('is_custom_price')->default(false); // Tambahkan ini
            $table->json('features')->nullable();
            $table->integer('delivery_time')->default(30)->comment('Delivery time in days');
            $table->integer('revision_limit')->default(3);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Add indexes
            $table->index('type');
            $table->index('is_active');
            $table->index('sort_order');
            $table->index(['is_active', 'sort_order']);
            $table->index('created_at');
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
