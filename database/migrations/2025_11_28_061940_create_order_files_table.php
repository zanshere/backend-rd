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
        Schema::create('order_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->bigInteger('file_size')->default(0);
            $table->enum('file_type', ['source_code', 'database', 'documentation', 'design', 'other'])->default('other');
            $table->text('description')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->string('version')->default('1.0');
            $table->timestamps();

            // Add indexes
            $table->index('order_id');
            $table->index('uploaded_by');
            $table->index('file_type');
            $table->index('file_name');
            $table->index('created_at');
            $table->index(['order_id', 'file_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_files');
    }
};
