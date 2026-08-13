@extends('layouts.guest')

@section('title', '500 Server Error')

@section('content')
<div class="container py-5 text-center">
    <div class="py-5">
        <i class="bi bi-exclamation-triangle" style="font-size:5rem;color:var(--lokabuy-orange);"></i>
        <h1 class="display-4 fw-bold mt-3">500</h1>
        <h4 class="text-muted mb-3">Something Went Wrong</h4>
        <p class="text-muted mb-4">We're experiencing a technical issue. Please try again in a moment.</p>
        <a href="{{ route('home') }}" class="btn btn-sell px-4">
            <i class="bi bi-house me-1"></i> Go to Homepage
        </a>
    </div>
</div>
@endsection
