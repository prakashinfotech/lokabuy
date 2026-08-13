@extends('layouts.admin')

@section('title', 'Listings')

@section('content')

{{-- Status tabs --}}
<ul class="nav nav-tabs mb-4">
    @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $val => $label)
    <li class="nav-item">
        <a href="{{ route('admin.listings.index', ['status' => $val]) }}"
           class="nav-link {{ $status === $val ? 'active fw-bold' : '' }}">
            {{ $label }}
        </a>
    </li>
    @endforeach
</ul>

@if($listings->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        No {{ $status }} listings.
    </div>
@else

<div class="d-flex flex-column gap-2">
    @foreach($listings as $listing)
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-3">
            <div class="d-flex align-items-center gap-3">

                {{-- Thumbnail --}}
                @if($listing->images->isNotEmpty())
                    <img src="{{ $listing->images->first()->url }}"
                         class="rounded-2 flex-shrink-0"
                         style="width:72px;height:56px;object-fit:cover">
                @else
                    <div class="rounded-2 flex-shrink-0 bg-light d-flex align-items-center justify-content-center"
                         style="width:72px;height:56px">
                        <i class="bi bi-image text-muted"></i>
                    </div>
                @endif

                {{-- Title + meta --}}
                <div class="flex-grow-1 min-w-0">
                    <div class="fw-semibold text-truncate" style="max-width:420px">{{ $listing->title }}</div>
                    <div class="text-muted small mt-1">
                        {{ $listing->location_city }}, {{ $listing->location_state }}
                        <span class="mx-1">·</span>
                        ₹{{ number_format($listing->price, 0) }}
                        @if($listing->is_featured)
                            <span class="badge ms-1" style="background:var(--lokabuy-orange);font-size:.65rem">Featured</span>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="d-flex gap-2 flex-shrink-0 flex-wrap justify-content-end">

                    {{-- View --}}
                    <button class="btn btn-sm btn-outline-secondary"
                            data-bs-toggle="modal"
                            data-bs-target="#detailModal-{{ $listing->id }}">
                        <i class="bi bi-eye me-1"></i>View
                    </button>

                    @if($listing->approval_status === 'pending')

                        {{-- Approve --}}
                        <form method="POST" action="{{ route('admin.listings.approve', $listing->id) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-success">
                                <i class="bi bi-check-lg me-1"></i>Approve
                            </button>
                        </form>

                        {{-- Reject --}}
                        <button class="btn btn-sm btn-outline-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#rejectModal-{{ $listing->id }}">
                            <i class="bi bi-x-lg me-1"></i>Reject
                        </button>

                    @elseif($listing->approval_status === 'approved')

                        {{-- Feature / Unfeature --}}
                        <form method="POST" action="{{ route('admin.listings.feature', $listing->id) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="is_featured" value="{{ $listing->is_featured ? '0' : '1' }}">
                            <button class="btn btn-sm {{ $listing->is_featured ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="bi bi-star{{ $listing->is_featured ? '-fill' : '' }} me-1"></i>
                                {{ $listing->is_featured ? 'Unfeature' : 'Feature' }}
                            </button>
                        </form>

                    @endif

                    {{-- Delete --}}
                    <form method="POST" action="{{ route('listings.destroy', $listing->slug) }}"
                          onsubmit="return confirm('Permanently delete this listing?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- Reject modal --}}
    @if($listing->approval_status === 'pending')
    <div class="modal fade" id="rejectModal-{{ $listing->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-3">
                <form method="POST" action="{{ route('admin.listings.reject', $listing->id) }}">
                    @csrf @method('PATCH')
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">Reject Listing</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-2">Rejecting: <strong>{{ $listing->title }}</strong></p>
                        <label class="form-label fw-semibold small">Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" rows="3" class="form-control" required
                                  placeholder="Explain why this listing is being rejected..."></textarea>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm fw-bold">Confirm Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Detail modal --}}
    <div class="modal fade" id="detailModal-{{ $listing->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow rounded-3">
                <div class="modal-header border-0 pb-0">
                    <div>
                        @if($listing->is_featured)
                            <span class="badge mb-1" style="background:var(--lokabuy-orange)">Featured</span>
                        @endif
                        <h5 class="modal-title fw-bold">{{ $listing->title }}</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body pt-2">

                    {{-- Image gallery --}}
                    @if($listing->images->isNotEmpty())
                    <div class="d-flex gap-2 mb-4 overflow-auto pb-1">
                        @foreach($listing->images as $img)
                        <img src="{{ $img->url }}" class="rounded flex-shrink-0"
                             style="height:160px;width:210px;object-fit:cover">
                        @endforeach
                    </div>
                    @else
                    <div class="d-flex align-items-center justify-content-center rounded-3 mb-4 bg-light"
                         style="height:100px">
                        <i class="bi bi-image text-muted fs-2"></i>
                    </div>
                    @endif

                    {{-- Info table --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless small mb-0">
                                <tr>
                                    <td class="text-muted ps-0" style="width:40%">Price</td>
                                    <td class="fw-semibold" style="color:var(--lokabuy-primary)">₹{{ number_format($listing->price, 0) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Category</td>
                                    <td>{{ $listing->category->name ?? '—' }}</td>
                                </tr>
                                @if($listing->subcategory)
                                <tr>
                                    <td class="text-muted ps-0">Subcategory</td>
                                    <td>{{ $listing->subcategory->name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="text-muted ps-0">Location</td>
                                    <td>{{ $listing->location_city }}, {{ $listing->location_state }}{{ $listing->location_country ? ', ' . $listing->location_country : '' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless small mb-0">
                                <tr>
                                    <td class="text-muted ps-0" style="width:40%">Seller</td>
                                    <td>{{ $listing->user->name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Posted</td>
                                    <td>{{ $listing->created_at->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Status</td>
                                    <td>{{ ucfirst($listing->status) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0">Approval</td>
                                    <td>
                                        <span class="badge {{ $listing->approval_status === 'approved' ? 'bg-success' : ($listing->approval_status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                            {{ ucfirst($listing->approval_status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Description --}}
                    <h6 class="fw-semibold mb-2">Description</h6>
                    <div class="text-muted small" style="white-space:pre-line;line-height:1.7">{{ $listing->description }}</div>

                </div>

                <div class="modal-footer border-0 d-flex flex-wrap gap-2">

                    @if($listing->approval_status === 'pending')
                    <form method="POST" action="{{ route('admin.listings.approve', $listing->id) }}">
                        @csrf @method('PATCH')
                        <button class="btn btn-success btn-sm fw-bold">
                            <i class="bi bi-check-lg me-1"></i>Approve
                        </button>
                    </form>
                    <button class="btn btn-danger btn-sm fw-bold" data-bs-dismiss="modal"
                            onclick="setTimeout(() => new bootstrap.Modal(document.getElementById('rejectModal-{{ $listing->id }}')).show(), 350)">
                        <i class="bi bi-x-lg me-1"></i>Reject
                    </button>
                    @endif

                    @if($listing->approval_status === 'approved')
                    <form method="POST" action="{{ route('admin.listings.feature', $listing->id) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="is_featured" value="{{ $listing->is_featured ? '0' : '1' }}">
                        <button class="btn btn-sm {{ $listing->is_featured ? 'btn-warning' : 'btn-outline-warning' }} fw-bold">
                            <i class="bi bi-star{{ $listing->is_featured ? '-fill' : '' }} me-1"></i>
                            {{ $listing->is_featured ? 'Unfeature' : 'Feature' }}
                        </button>
                    </form>
                    @endif

                    <form method="POST" action="{{ route('listings.destroy', $listing->slug) }}"
                          onsubmit="return confirm('Permanently delete this listing? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm fw-bold">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </form>

                    <button type="button" class="btn btn-outline-secondary btn-sm ms-auto"
                            data-bs-dismiss="modal">Close</button>
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

@endsection
