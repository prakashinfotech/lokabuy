@extends('layouts.app')

@section('title', 'All Categories')

@section('content')
<div class="container py-4">

    <h4 class="fw-bold mb-4" style="color:var(--lokabuy-primary)">Browse Categories</h4>

    <div class="row g-3">
        @foreach($categories as $category)
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <a href="{{ route('categories.show', $category->slug) }}"
               class="text-decoration-none">
                <div class="card border-0 shadow-sm text-center p-3 h-100 category-card">
                    <i class="{{ $category->icon }} fs-2 mb-2" style="color:var(--lokabuy-primary)"></i>
                    <div class="fw-semibold small text-dark">{{ $category->name }}</div>
                    <div class="text-muted" style="font-size:0.75rem">
                        {{ number_format($category->listings_count) }} ads
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

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
