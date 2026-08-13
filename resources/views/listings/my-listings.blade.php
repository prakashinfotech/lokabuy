@extends('layouts.app')

@section('title', 'My Ads')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color:var(--lokabuy-primary)">My Ads</h4>
        <a href="{{ route('listings.create') }}" class="btn fw-bold text-white px-4"
           style="background:var(--lokabuy-primary)">
            <i class="bi bi-plus-lg me-1"></i>Post Ad
        </a>
    </div>


    @if($listings->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-megaphone fs-1 text-muted"></i>
        <p class="text-muted mt-3">You haven't posted any ads yet.</p>
        <a href="{{ route('listings.create') }}" class="btn fw-bold text-white px-4"
           style="background:var(--lokabuy-primary)">Post Your First Ad</a>
    </div>
    @else

    <div class="row g-3">
        @foreach($listings as $listing)
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="row align-items-center g-3">

                        {{-- Thumbnail --}}
                        <div class="col-auto">
                            @if($listing->images->isNotEmpty())
                                <img src="{{ $listing->images->first()->url }}"
                                     class="rounded" width="90" height="70"
                                     style="object-fit:cover">
                            @else
                                <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                     style="width:90px;height:70px">
                                    <i class="bi bi-image text-muted fs-4"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="col">
                            <div class="fw-semibold">{{ $listing->title }}</div>
                            <div class="text-muted small">
                                {{ $listing->category->name ?? 'â€”' }} &middot;
                                {{ $listing->location_city }}, {{ $listing->location_state }}
                            </div>
                            <div class="fw-bold" style="color:var(--lokabuy-primary)">
                                ₹{{ number_format($listing->price, 2) }}
                            </div>
                            <div class="mt-1 d-flex flex-wrap gap-1">
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
                                @if($listing->is_featured)
                                    <span class="badge" style="background:var(--lokabuy-orange)">Featured</span>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="col-auto d-flex flex-column gap-2">
                            <a href="{{ route('listings.edit', $listing->slug) }}"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>

                            @if($listing->status === 'active')
                            <form method="POST" action="{{ route('listings.sold', $listing->slug) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-warning w-100">Mark Sold</button>
                            </form>
                            @endif

                            @if(in_array($listing->status, ['sold', 'expired']))
                            <form method="POST" action="{{ route('listings.renew', $listing->slug) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-success w-100">Renew</button>
                            </form>
                            @endif

                            <form method="POST" action="{{ route('listings.destroy', $listing->slug) }}"
                                  onsubmit="return confirm('Delete this ad?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger w-100">Delete</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        @include('partials._pagination', ['paginator' => $listings])
    </div>

    @endif

</div>
@endsection
