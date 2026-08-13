@extends('layouts.guest')

@section('title', $page->title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <h2 class="fw-bold mb-4" style="color:var(--lokabuy-primary)">{{ $page->title }}</h2>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4 p-lg-5">
                    <div class="page-content" style="line-height:1.8">
                        {!! $page->content !!}
                    </div>
                    <hr class="mt-4">
                    <p class="text-muted small mb-0">Last updated: {{ $page->updated_at->format('d M Y') }}</p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
