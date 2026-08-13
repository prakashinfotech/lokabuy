<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Admin\AdminCategoryController;
use App\Http\Controllers\Api\V1\Admin\AdminDashboardController;
use App\Http\Controllers\Api\V1\Admin\AdminListingController;
use App\Http\Controllers\Api\V1\Admin\AdminReportController;
use App\Http\Controllers\Api\V1\Admin\AdminUserController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\Listing\ListingController;
use App\Http\Controllers\Api\V1\Listing\ListingImageController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\User\DashboardController;
use App\Http\Controllers\Api\V1\User\FavoriteController;
use App\Http\Controllers\Api\V1\User\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — /api/v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // ── Auth (unauthenticated) ──────────────────────────────────────────
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::post('forgot-password', [PasswordResetController::class, 'forgot'])->name('forgot');
        Route::post('reset-password', [PasswordResetController::class, 'reset'])->name('reset');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    });

    // ── Categories (public) ─────────────────────────────────────────────
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

    // ── Listings (public) ───────────────────────────────────────────────
    Route::get('listings', [ListingController::class, 'index'])->name('listings.index');
    Route::get('listings/autocomplete', [ListingController::class, 'autocomplete'])->name('listings.autocomplete');
    Route::get('listings/{listing}', [ListingController::class, 'show'])->name('listings.show');

    // ── Authenticated routes ────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Profile & Dashboard
        Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');
        Route::get('my/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('my/listings', [DashboardController::class, 'listings'])->name('my.listings');

        // Listings — write (create requires verified email)
        Route::post('listings', [ListingController::class, 'store'])
            ->middleware('verified')
            ->name('listings.store');
        Route::put('listings/{listing}', [ListingController::class, 'update'])->name('listings.update');
        Route::delete('listings/{listing}', [ListingController::class, 'destroy'])->name('listings.destroy');
        Route::patch('listings/{listing}/sold', [ListingController::class, 'markAsSold'])->name('listings.sold');
        Route::patch('listings/{listing}/renew', [ListingController::class, 'renew'])->name('listings.renew');

        // Listing images
        Route::post('listings/{listing}/images', [ListingImageController::class, 'store'])->name('listing-images.store');
        Route::delete('listings/{listing}/images/{image}', [ListingImageController::class, 'destroy'])->name('listing-images.destroy');

        // Favorites
        Route::get('favorites', [FavoriteController::class, 'index'])->name('favorites.index');
        Route::post('favorites/{listing}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

        // Reports
        Route::post('listings/{listing}/report', [ReportController::class, 'store'])->name('listings.report');

        // Admin routes
        Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
            Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

            Route::get('listings/pending', [AdminListingController::class, 'pending'])->name('listings.pending');
            Route::patch('listings/{listing}/approve', [AdminListingController::class, 'approve'])->name('listings.approve');
            Route::patch('listings/{listing}/reject', [AdminListingController::class, 'reject'])->name('listings.reject');
            Route::patch('listings/{listing}/feature', [AdminListingController::class, 'setFeatured'])->name('listings.feature');

            Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
            Route::patch('reports/{report}/action', [AdminReportController::class, 'action'])->name('reports.action');
            Route::patch('reports/{report}/dismiss', [AdminReportController::class, 'dismiss'])->name('reports.dismiss');

            Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
            Route::patch('users/{user}/deactivate', [AdminUserController::class, 'deactivate'])->name('users.deactivate');

            Route::get('categories', [AdminCategoryController::class, 'index'])->name('categories.index');
            Route::post('categories', [AdminCategoryController::class, 'store'])->name('categories.store');
            Route::put('categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        });
    });
});

// Fallback — undefined API routes
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'data' => null,
        'error' => 'Route not found',
    ], 404);
});
