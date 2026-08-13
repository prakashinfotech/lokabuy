@extends('layouts.guest')

@section('title', request('q') ? 'Search: ' . request('q') : 'Browse Ads')

@section('content')
<div class="container py-4">

    {{-- Active filters banner --}}
    @if(request()->anyFilled(['q','category','subcategory','city','state','country','price_min','price_max']))
    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
        <span class="text-muted small">Filters:</span>
        @if(request('q'))
            <span class="badge bg-secondary">{{ request('q') }}
                <a href="{{ route('listings.index', request()->except(['q'])) }}" class="text-white ms-1">&times;</a>
            </span>
        @endif
        @if(request('category'))
            <span class="badge bg-secondary">{{ request('category') }}
                <a href="{{ route('listings.index', request()->except(['category','subcategory'])) }}" class="text-white ms-1">&times;</a>
            </span>
        @endif
        @if(request('subcategory'))
            <span class="badge bg-secondary">{{ request('subcategory') }}
                <a href="{{ route('listings.index', request()->except(['subcategory'])) }}" class="text-white ms-1">&times;</a>
            </span>
        @endif
        @if(request('city'))
            <span class="badge bg-secondary">{{ request('city') }}
                <a href="{{ route('listings.index', request()->except(['city'])) }}" class="text-white ms-1">&times;</a>
            </span>
        @endif
        @if(request('state'))
            <span class="badge bg-secondary">{{ request('state') }}
                <a href="{{ route('listings.index', request()->except(['state'])) }}" class="text-white ms-1">&times;</a>
            </span>
        @endif
        @if(request('country'))
            <span class="badge bg-secondary">{{ request('country') }}
                <a href="{{ route('listings.index', request()->except(['country'])) }}" class="text-white ms-1">&times;</a>
            </span>
        @endif
        @if(request('price_min') || request('price_max'))
            <span class="badge bg-secondary">
                ₹{{ request('price_min') ?: '0' }} – {{ request('price_max') ? '₹' . request('price_max') : 'any' }}
                <a href="{{ route('listings.index', request()->except(['price_min','price_max'])) }}" class="text-white ms-1">&times;</a>
            </span>
        @endif
        <a href="{{ route('listings.index') }}" class="small text-danger">Clear all</a>
    </div>
    @endif

    <div class="row g-4">

        {{-- Filter sidebar --}}
        <div class="col-lg-3" x-data="{ open: false }">

            {{-- Mobile toggle --}}
            <button class="btn btn-outline-secondary w-100 d-lg-none mb-3" @click="open = !open">
                <i class="bi bi-funnel me-1"></i>Filters
            </button>

            <div :class="open ? 'd-block' : 'd-none d-lg-block'">
                <form method="GET" action="{{ route('listings.index') }}" id="filter-form">

                    {{-- Preserve keyword --}}
                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif

                    <div class="card border-0 shadow-sm rounded-3 mb-3">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-3">Category</h6>
                            <select name="category" class="form-select form-select-sm mb-2"
                                    onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->slug }}"
                                            {{ request('category') === $cat->slug ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if($subcategories->isNotEmpty())
                            <select name="subcategory" class="form-select form-select-sm"
                                    onchange="this.form.submit()">
                                <option value="">All Subcategories</option>
                                @foreach($subcategories as $sub)
                                    <option value="{{ $sub->slug }}"
                                            {{ request('subcategory') === $sub->slug ? 'selected' : '' }}>
                                        {{ $sub->name }}
                                    </option>
                                @endforeach
                            </select>
                            @endif
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-3 mb-3">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-3">Location</h6>
                            <input type="text" name="city" class="form-control form-control-sm mb-2"
                                   placeholder="City" value="{{ request('city') }}">
                            <input type="text" name="state" class="form-control form-control-sm mb-2"
                                   placeholder="State" value="{{ request('state') }}">
                            <select name="country" class="form-select form-select-sm">
                                <option value="">All Countries</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country }}" {{ request('country') === $country ? 'selected' : '' }}>
                                        {{ $country }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-3 mb-3">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-3">Price Range (₹)</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="price_min" class="form-control form-control-sm"
                                           placeholder="Min" min="0" value="{{ request('price_min') }}">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="price_max" class="form-control form-control-sm"
                                           placeholder="Max" min="0" value="{{ request('price_max') }}">
                                </div>
                            </div>
                        </div>
                    </div>


                    <button type="submit" class="btn w-100 fw-bold text-white"
                            style="background:var(--lokabuy-primary)">Apply Filters</button>

                </form>
            </div>
        </div>

        {{-- Results --}}
        <div class="col-lg-9">

            {{-- Header row --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="text-muted small">
                    {{ number_format($listings->total()) }} ad{{ $listings->total() !== 1 ? 's' : '' }} found
                    @if(request('q')) for "<strong>{{ request('q') }}</strong>" @endif
                </div>

                <form method="GET" action="{{ route('listings.index') }}" id="sort-form">
                    @foreach(request()->except('sort') as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                    <select name="sort" class="form-select form-select-sm" style="width:auto"
                            onchange="document.getElementById('sort-form').submit()">
                        <option value="newest"     {{ request('sort','newest') === 'newest'     ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest"     {{ request('sort') === 'oldest'     ? 'selected' : '' }}>Oldest First</option>
                        <option value="price_asc"  {{ request('sort') === 'price_asc'  ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </form>
            </div>

            @if($listings->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-search fs-1 text-muted"></i>
                    <p class="text-muted mt-3">No ads found matching your search.</p>
                    <a href="{{ route('listings.index') }}" class="btn btn-outline-secondary btn-sm">Clear filters</a>
                </div>
            @else
                <div class="row g-3">
                    @foreach($listings as $listing)
                    <div class="col-sm-6 col-xl-4">
                        @include('partials._listing-card', ['listing' => $listing])
                    </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    @include('partials._pagination', ['paginator' => $listings])
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
