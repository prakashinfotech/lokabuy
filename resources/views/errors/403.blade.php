@extends('layouts.guest')

@section('title', '403 Forbidden')

@section('content')
<div class="container py-5 text-center">
    <div class="py-5">
        <i class="bi bi-shield-exclamation" style="font-size:5rem;color:var(--lokabuy-primary);"></i>
        <h1 class="display-4 fw-bold mt-3">403</h1>
        <h4 class="text-muted mb-3">Access Forbidden</h4>
        <p class="text-muted mb-4">You don't have permission to access this page.</p>
        <a href="{{ route('home') }}" class="btn btn-sell px-4">
            <i class="bi bi-house me-1"></i> Go to Homepage
        </a>
    </div>
</div>
@endsection
