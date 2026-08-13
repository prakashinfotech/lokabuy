<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Listing;
use App\Models\ListingImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ImageService
{
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp'];

    private const MAX_BYTES = 5 * 1024 * 1024; // 5 MB

    private const MAX_PER_LISTING = 10;

    private const AVATAR_MAX_BYTES = 2 * 1024 * 1024; // 2 MB

    public function store(Listing $listing, UploadedFile $file): ListingImage
    {
        $this->validateImage($file, self::MAX_BYTES);

        $path = $file->store("listings/{$listing->id}", 'public');

        $order = $listing->images()->max('order') ?? -1;

        return $listing->images()->create([
            'url' => Storage::disk('public')->url($path),
            'order' => $order + 1,
        ]);
    }

    public function delete(ListingImage $image): void
    {
        $this->deleteFromStorage($image->url);
        $image->delete();
    }

    public function deleteAll(Listing $listing): void
    {
        foreach ($listing->images as $image) {
            $this->deleteFromStorage($image->url);
        }

        $listing->images()->delete();
    }

    public function storeAvatar(int $userId, UploadedFile $file): string
    {
        $this->validateImage($file, self::AVATAR_MAX_BYTES);

        $path = $file->store("avatars/{$userId}", 'public');

        return Storage::disk('public')->url($path);
    }

    public function canAddImages(Listing $listing): bool
    {
        return $listing->images()->count() < self::MAX_PER_LISTING;
    }

    public function maxPerListing(): int
    {
        return self::MAX_PER_LISTING;
    }

    private function validateImage(UploadedFile $file, int $maxBytes): void
    {
        $mbLimit = (int) ($maxBytes / 1024 / 1024);

        if (! in_array($file->getMimeType(), self::ALLOWED_MIMES, true)) {
            throw ValidationException::withMessages([
                'image' => ["Invalid image: must be JPEG, PNG, or WebP and under {$mbLimit} MB"],
            ]);
        }

        if ($file->getSize() > $maxBytes) {
            throw ValidationException::withMessages([
                'image' => ["Invalid image: must be JPEG, PNG, or WebP and under {$mbLimit} MB"],
            ]);
        }
    }

    private function deleteFromStorage(string $url): void
    {
        $path = str_replace(Storage::disk('public')->url(''), '', $url);

        try {
            Storage::disk('public')->delete($path);
        } catch (\Throwable $e) {
            Log::error('Failed to delete image from storage', ['path' => $path, 'error' => $e->getMessage()]);
        }
    }
}
