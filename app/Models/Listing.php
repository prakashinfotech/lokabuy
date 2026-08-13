<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'subcategory_id',
        'title',
        'slug',
        'description',
        'price',
        'currency',
        'listing_type',
        'location_city',
        'location_state',
        'location_country',
        'location_lat',
        'location_lng',
        'status',
        'approval_status',
        'is_featured',
        'featured_until',
        'view_count',
        'expires_at',
    ];

    protected $casts = [
        'location_lat' => 'decimal:7',
        'location_lng' => 'decimal:7',
        'is_featured' => 'boolean',
        'view_count' => 'integer',
        'featured_until' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ListingImage::class)->orderBy('order');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)
            ->where(function (Builder $q) {
                $q->whereNull('featured_until')
                    ->orWhere('featured_until', '>', now());
            });
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->active()->approved();
    }
}
