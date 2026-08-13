@extends('layouts.guest')

@section('title', 'Lokabuy — Buy & Sell Online')

@section('content')

{{-- Hero --}}
<section class="hero-section py-5" style="background:linear-gradient(135deg,var(--lokabuy-primary) 0%,#003d6b 100%)">
    <div class="container">
        <div class="row justify-content-center text-center mb-4">
            <div class="col-lg-8">
                <h1 class="fw-bold text-white mb-2">Buy &amp; Sell Anything</h1>
                <p class="text-white-50 mb-0">Millions of ads across India — find your next deal today.</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <form action="{{ route('listings.index') }}" method="GET">
                    <div class="input-group input-group-lg shadow">
                        <input
                            type="text"
                            name="q"
                            class="form-control border-0 rounded-start-3"
                            placeholder="Search for cars, mobiles, jobs…"
                            value="{{ request('q') }}"
                            autocomplete="off"
                        >
                        <button class="btn px-4 rounded-end-3 fw-semibold"
                                style="background:var(--lokabuy-orange);color:#fff"
                                type="submit">
                            <i class="bi bi-search me-1"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- Categories --}}
@if($categories->isNotEmpty())
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="fw-bold mb-4" style="font-size:1.25rem">Browse by Category</h2>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3">
            @foreach($categories as $category)
            <div class="col">
                <a href="{{ route('categories.show', $category->slug) }}"
                   class="text-decoration-none">
                    <div class="card border-0 shadow-sm text-center py-3 px-2 h-100 category-card">
                        <div class="mb-2">
                            @if($category->icon)
                                <i class="{{ $category->icon }} fs-2" style="color:var(--lokabuy-primary)"></i>
                            @else
                                <i class="bi bi-grid fs-2" style="color:var(--lokabuy-primary)"></i>
                            @endif
                        </div>
                        <div class="fw-semibold small text-dark">{{ $category->name }}</div>
                        <div class="text-muted" style="font-size:0.7rem">
                            {{ number_format($category->listings_count) }} ads
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Featured Listings --}}
@if($featured->isNotEmpty())
<section class="py-5">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h2 class="fw-bold mb-0" style="font-size:1.25rem">
                <i class="bi bi-star-fill me-1" style="color:var(--lokabuy-orange)"></i> Featured Ads
            </h2>
            <a href="{{ route('listings.index') }}" class="small fw-semibold text-decoration-none" style="color:var(--lokabuy-primary)">
                View all <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
            @foreach($featured as $listing)
            <div class="col">
                @include('partials._listing-card', ['listing' => $listing])
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Recent Listings --}}
@if($recent->isNotEmpty())
<section class="py-4 @if($featured->isEmpty()) pt-5 @endif bg-light">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h2 class="fw-bold mb-0" style="font-size:1.25rem">
                <i class="bi bi-clock me-1" style="color:var(--lokabuy-primary)"></i> Recently Added
            </h2>
            <a href="{{ route('listings.index') }}" class="small fw-semibold text-decoration-none" style="color:var(--lokabuy-primary)">
                View all <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
            @foreach($recent as $listing)
            <div class="col">
                @include('partials._listing-card', ['listing' => $listing])
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Empty state --}}
@if($featured->isEmpty() && $recent->isEmpty())
<section class="py-5">
    <div class="container text-center py-5">
        <i class="bi bi-shop display-1 text-muted"></i>
        <h3 class="mt-3 fw-semibold">No ads yet</h3>
        <p class="text-muted">Be the first to post an ad!</p>
        <a href="{{ route('listings.create') }}" class="btn btn-lg fw-semibold px-4"
           style="background:var(--lokabuy-orange);color:#fff">
            Post a Free Ad
        </a>
    </div>
</section>
@endif

{{-- Post CTA --}}
<section class="py-5" style="background:var(--lokabuy-primary)">
    <div class="container text-center">
        <h2 class="fw-bold text-white mb-2">Ready to sell?</h2>
        <p class="text-white-50 mb-4">Post your ad for free in under 2 minutes.</p>
        <a href="{{ route('listings.create') }}"
           class="btn btn-lg fw-semibold px-5 rounded-pill"
           style="background:var(--lokabuy-orange);color:#fff">
            <i class="bi bi-plus-circle me-1"></i> Post a Free Ad
        </a>
    </div>
</section>

@endsection

@push('styles')
<style>
    .hero-section { min-height: 220px; display: flex; align-items: center; }
    .category-card { transition: transform .15s, box-shadow .15s; cursor: pointer; }
    .category-card:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.12) !important; }
</style>
@endpush
