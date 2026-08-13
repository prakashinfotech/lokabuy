<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListingsTable extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 100);
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 12, 2);
            $table->string('currency', 3)->default('INR');
            $table->enum('listing_type', ['buy', 'sell', 'rent'])->nullable();
            $table->string('location_city', 100);
            $table->string('location_state', 100);
            $table->decimal('location_lat', 10, 7)->nullable();
            $table->decimal('location_lng', 10, 7)->nullable();
            $table->enum('status', ['active', 'sold', 'expired', 'deleted', 'inactive'])->default('inactive');
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('featured_until')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'approval_status']);
            $table->index(['category_id', 'status']);
            $table->index('user_id');
            $table->index('expires_at');
            $table->index('is_featured');
        });

        DB::statement('ALTER TABLE listings ADD FULLTEXT fulltext_listings_title_description (title, description)');
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
}
