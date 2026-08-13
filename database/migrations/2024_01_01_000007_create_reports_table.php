<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->enum('reason', ['spam', 'fraud', 'prohibited', 'duplicate', 'other']);
            $table->enum('status', ['pending', 'actioned', 'dismissed'])->default('pending');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['status']);
            $table->index(['listing_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
}
