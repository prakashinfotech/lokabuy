<?php

declare(strict_types=1);

use App\Http\Controllers\Web\Admin\AdminCategoryController;
use App\Http\Controllers\Web\Admin\AdminDashboardController;
use App\Http\Controllers\Web\Admin\AdminListingController;
use App\Http\Controllers\Web\Admin\AdminPageController;
use App\Http\Controllers\Web\Admin\AdminReportController;
use App\Http\Controllers\Web\Admin\AdminUserController;
use App\Http\Controllers\Web\Auth\AuthController;
use App\Http\Controllers\Web\Auth\ForgotPasswordController;
use App\Http\Controllers\Web\Auth\ResetPasswordController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\Listing\ListingController;
use App\Http\Controllers\Web\Listing\ReportController;
use App\Http\Controllers\Web\User\DashboardController;
use App\Http\Controllers\Web\User\FavoriteController;
use App\Http\Controllers\Web\User\ProfileController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Guest-only routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', fn () => view('auth.passwords.email'))->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Email verification routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('home')->with('status', 'Email verified successfully!');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Listings — write (create requires verified email)
    Route::middleware('verified')->group(function () {
        Route::get('/sell', [ListingController::class, 'create'])->name('listings.create');
        Route::post('/sell', [ListingController::class, 'store'])->name('listings.store');
    });
    Route::get('/listings/{listing:slug}/edit', [ListingController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{listing:slug}', [ListingController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{listing:slug}', [ListingController::class, 'destroy'])->name('listings.destroy');
    Route::post('/listings/{listing:slug}/report', [ReportController::class, 'store'])->name('listings.report');
    Route::patch('/listings/{listing:slug}/sold', [ListingController::class, 'markAsSold'])->name('listings.sold');
    Route::patch('/listings/{listing:slug}/renew', [ListingController::class, 'renew'])->name('listings.renew');
    Route::delete('/listings/{listing:slug}/images/{image}', [ListingController::class, 'destroyImage'])->name('listings.images.destroy');
    Route::get('/my/ads', [ListingController::class, 'myListings'])->name('listings.my');

    // Favourites
    Route::get('/favourites', [FavoriteController::class, 'index'])->name('favorites.index');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Listings approval queue
    Route::get('listings', [AdminListingController::class, 'index'])->name('listings.index');
    Route::patch('listings/{listing:id}/approve', [AdminListingController::class, 'approve'])->name('listings.approve');
    Route::patch('listings/{listing:id}/reject', [AdminListingController::class, 'reject'])->name('listings.reject');
    Route::patch('listings/{listing:id}/feature', [AdminListingController::class, 'setFeatured'])->name('listings.feature');

    // Reports
    Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::patch('reports/{report}/action', [AdminReportController::class, 'action'])->name('reports.action');
    Route::patch('reports/{report}/dismiss', [AdminReportController::class, 'dismiss'])->name('reports.dismiss');

    // Users
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('users/{user}/deactivate', [AdminUserController::class, 'deactivate'])->name('users.deactivate');
    Route::patch('users/{user}/activate', [AdminUserController::class, 'activate'])->name('users.activate');

    // Categories
    Route::get('categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');

    // Subcategory management
    Route::post('categories/{category}/subcategories', [AdminCategoryController::class, 'storeSubcategory'])->name('categories.subcategories.store');
    Route::put('categories/{category}/subcategories/{subcategory}', [AdminCategoryController::class, 'updateSubcategory'])->name('categories.subcategories.update');
    Route::patch('categories/{category}/subcategories/{subcategory}/toggle', [AdminCategoryController::class, 'toggleSubcategory'])->name('categories.subcategories.toggle');

    // Pages
    Route::get('pages', [AdminPageController::class, 'index'])->name('pages.index');
    Route::get('pages/{page:slug}/edit', [AdminPageController::class, 'edit'])->name('pages.edit');
    Route::put('pages/{page:slug}', [AdminPageController::class, 'update'])->name('pages.update');
});

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Listings — public browse & detail
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::get('/listings/{listing:slug}', [ListingController::class, 'show'])->name('listings.show');

// Categories
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// Static pages
Route::get('/pages/{page:slug}', [PageController::class, 'show'])->name('pages.show');
