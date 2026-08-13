@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="container py-4">

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('categories.index') }}" style="color:var(--lokabuy-primary)">Categories</a>
            </li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center gap-3 mb-4">
        <i class="{{ $category->icon }} fs-2" style="color:var(--lokabuy-primary)"></i>
        <h4 class="fw-bold mb-0" style="color:var(--lokabuy-primary)">{{ $category->name }}</h4>
    </div>

    @if($category->subcategories->isNotEmpty())
    <div class="row g-3">
        @foreach($category->subcategories as $sub)
        <div class="col-6 col-sm-4 col-md-3">
            <a href="{{ route('listings.index', ['subcategory' => $sub->slug]) }}"
               class="text-decoration-none">
                <div class="card border-0 shadow-sm p-3 h-100 category-card">
                    <div class="fw-semibold small text-dark">{{ $sub->name }}</div>
                    <div class="text-muted" style="font-size:0.75rem">
                        {{ number_format($sub->listings_count) }} ads
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
    @else
    <p class="text-muted">No subcategories found.</p>
    @endif

</div>
@endsection

@push('styles')
<style>
.category-card {
    transition: transform .15s, box-shadow .15s;
    cursor: pointer;
}
.category-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0,0,0,.1) !important;
}
</style>
@endpush
