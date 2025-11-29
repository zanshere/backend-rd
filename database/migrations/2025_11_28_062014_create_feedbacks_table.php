<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['suggestion', 'complaint', 'bug', 'feature', 'other']);
            $table->text('message');
            $table->integer('rating')->nullable();
            $table->enum('status', ['pending', 'read', 'archived'])->default('pending');
            $table->text('response')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['type', 'status']);
            $table->index('created_at');
            $table->index('rating');
        });
    }

    public function down()
    {
        Schema::dropIfExists('feedbacks');
    }
};
