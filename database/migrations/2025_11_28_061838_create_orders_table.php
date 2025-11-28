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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->string('custom_package_name')->nullable();
            $table->json('custom_features')->nullable();
            $table->text('description')->nullable();
            $table->decimal('total_price', 15, 0)->default(0);
            $table->decimal('paid_amount', 15, 0)->default(0);
            $table->enum('status', ['pending', 'accepted', 'progress', 'revision', 'completed', 'cancelled', 'rejected'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'partial', 'failed', 'refunded'])->default('pending');
            $table->integer('progress_percentage')->default(0);
            $table->timestamp('deadline')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('customer_notes')->nullable();
            $table->json('special_requirements')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Add indexes
            $table->index('order_number');
            $table->index('user_id');
            $table->index('package_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('progress_percentage');
            $table->index('deadline');
            $table->index('completed_at');
            $table->index(['status', 'payment_status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
