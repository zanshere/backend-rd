// database/migrations/2025_11_28_061838_create_orders_table.php (Update)
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->string('project_name');
            $table->string('domain_name');
            $table->text('special_requirements')->nullable();
            $table->decimal('base_price', 15, 0)->default(0);
            $table->decimal('discount_amount', 15, 0)->default(0);
            $table->decimal('total_price', 15, 0)->default(0);
            $table->enum('status', ['draft', 'pending', 'confirmed', 'progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'expired'])->default('pending');
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('midtrans_order_id')->nullable();
            $table->string('midtrans_merchant_id')->nullable();
            $table->text('payment_url')->nullable();
            $table->decimal('paid_amount', 15, 0)->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Add indexes
            $table->index('order_number');
            $table->index('user_id');
            $table->index('package_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
