@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container py-4">

    <h4 class="fw-bold mb-4" style="color:var(--lokabuy-primary)">
        Welcome back, {{ Str::words(auth()->user()->name, 1, '') }}!
    </h4>


    {{-- Stats row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 text-center p-3">
                <div class="fs-2 fw-bold" style="color:var(--lokabuy-primary)">{{ $stats['active_listings'] }}</div>
                <div class="text-muted small mt-1">Active Ads</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 text-center p-3">
                <div class="fs-2 fw-bold text-warning">{{ $stats['pending_listings'] }}</div>
                <div class="text-muted small mt-1">Pending Approval</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 text-center p-3">
                <div class="fs-2 fw-bold text-success">{{ $stats['sold_listings'] }}</div>
                <div class="text-muted small mt-1">Sold</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 text-center p-3">
                <div class="fs-2 fw-bold text-info">{{ number_format($stats['total_views']) }}</div>
                <div class="text-muted small mt-1">Total Views</div>
            </div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('listings.create') }}" class="btn fw-bold text-white px-4"
           style="background:var(--lokabuy-primary)">
            <i class="bi bi-plus-lg me-1"></i>Post New Ad
        </a>
        <a href="{{ route('listings.my') }}" class="btn btn-outline-secondary px-4">
            <i class="bi bi-megaphone me-1"></i>Manage Ads
        </a>
<a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary px-4">
            <i class="bi bi-gear me-1"></i>Edit Profile
        </a>
    </div>

    {{-- Recent ads --}}
    @if($recentListings->isNotEmpty())
    <h5 class="fw-bold mb-3">Recent Ads</h5>
    <div class="card border-0 shadow-sm rounded-3">
        <div class="list-group list-group-flush rounded-3">
            @foreach($recentListings as $listing)
            <div class="list-group-item px-4 py-3">
                <div class="d-flex align-items-center gap-3">
                    {{-- Thumbnail --}}
                    @if($listing->images->isNotEmpty())
                        <img src="{{ $listing->images->first()->url }}" class="rounded"
                             width="56" height="44" style="object-fit:cover;flex-shrink:0">
                    @else
                        <div class="rounded bg-light d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:56px;height:44px">
                            <i class="bi bi-image text-muted"></i>
                        </div>
                    @endif

                    {{-- Info --}}
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold text-truncate small">{{ $listing->title }}</div>
                        <div class="text-muted" style="font-size:0.75rem">
                            ₹{{ number_format($listing->price, 0) }} &middot;
                            {{ $listing->created_at->diffForHumans() }}
                        </div>
                    </div>

                    {{-- Badges --}}
                    <div class="d-flex gap-1 flex-shrink-0">
                        @php
                            $statusClass = match($listing->status) {
                                'active'   => 'bg-success',
                                'inactive' => 'bg-secondary',
                                'sold'     => 'bg-warning text-dark',
                                'expired'  => 'bg-danger',
                                default    => 'bg-secondary',
                            };
                            $approvalClass = match($listing->approval_status) {
                                'approved' => 'bg-success',
                                'pending'  => 'bg-warning text-dark',
                                'rejected' => 'bg-danger',
                                default    => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst($listing->status) }}</span>
                        <span class="badge {{ $approvalClass }}">{{ ucfirst($listing->approval_status) }}</span>
                    </div>

                    <a href="{{ route('listings.edit', $listing->slug) }}"
                       class="btn btn-sm btn-outline-secondary flex-shrink-0">
                        Edit
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('listings.my') }}" class="small" style="color:var(--lokabuy-primary)">
            View all my ads <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    @else
    <div class="text-center py-4 text-muted">
        <i class="bi bi-megaphone fs-2 d-block mb-2"></i>
        You haven't posted any ads yet.
        <a href="{{ route('listings.create') }}" style="color:var(--lokabuy-primary)">Post your first ad</a>
    </div>
    @endif

</div>
@endsection
