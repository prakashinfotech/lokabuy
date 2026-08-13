@extends('layouts.guest')

@section('title', $listing->title)

@section('content')
<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('listings.index') }}" style="color:var(--lokabuy-primary)">Ads</a></li>
            @if($listing->category)
                <li class="breadcrumb-item">
                    <a href="{{ route('listings.index', ['category' => $listing->category->slug]) }}"
                       style="color:var(--lokabuy-primary)">{{ $listing->category->name }}</a>
                </li>
            @endif
            @if($listing->subcategory)
                <li class="breadcrumb-item">
                    <a href="{{ route('listings.index', ['subcategory' => $listing->subcategory->slug]) }}"
                       style="color:var(--lokabuy-primary)">{{ $listing->subcategory->name }}</a>
                </li>
            @endif
            <li class="breadcrumb-item active text-truncate" style="max-width:200px">{{ $listing->title }}</li>
        </ol>
    </nav>

    <div class="row g-4">

        {{-- Left: images + details --}}
        <div class="col-lg-8">

            {{-- Image gallery --}}
            @if($listing->images->isNotEmpty())
            <div class="card border-0 shadow-sm rounded-3 mb-4"
                 x-data="{ active: 0, images: {{ $listing->images->pluck('url')->toJson() }} }">

                {{-- Main image --}}
                <div class="position-relative rounded-top-3 overflow-hidden" style="height:400px;background:#f8f9fa">
                    <img :src="images[active]"
                         class="w-100 h-100"
                         style="object-fit:contain"
                         alt="{{ $listing->title }}">

                    @if($listing->images->count() > 1)
                    <button type="button"
                            class="btn btn-dark btn-sm opacity-75 position-absolute top-50 start-0 translate-middle-y ms-2"
                            @click="active = (active - 1 + images.length) % images.length">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button type="button"
                            class="btn btn-dark btn-sm opacity-75 position-absolute top-50 end-0 translate-middle-y me-2"
                            @click="active = (active + 1) % images.length">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                    @endif

                    <span class="position-absolute bottom-0 end-0 m-2 badge bg-dark bg-opacity-50 small"
                          x-text="(active + 1) + ' / ' + images.length"></span>
                </div>

                {{-- Thumbnails --}}
                @if($listing->images->count() > 1)
                <div class="d-flex gap-2 p-3 flex-wrap">
                    <template x-for="(url, i) in images" :key="i">
                        <img :src="url" @click="active = i"
                             class="rounded"
                             :class="active === i ? 'border border-2 opacity-100' : 'opacity-50'"
                             :style="active === i ? { borderColor: 'var(--lokabuy-primary)' } : {}"
                             style="width:64px;height:48px;object-fit:cover;cursor:pointer"
                             alt="">
                    </template>
                </div>
                @endif
            </div>
            @else
            <div class="card border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center justify-content-center"
                 style="height:300px;background:#f8f9fa">
                <i class="bi bi-image text-muted fs-1"></i>
            </div>
            @endif

            {{-- Listing details --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            @if($listing->is_featured)
                                <span class="badge mb-1" style="background:var(--lokabuy-orange)">Featured</span>
                            @endif
                            <h4 class="fw-bold mb-0">{{ $listing->title }}</h4>
                        </div>
                        <div class="fs-4 fw-bold" style="color:var(--lokabuy-primary)">
                            ₹{{ number_format($listing->price, 0) }}
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3 text-muted small mb-4">
                        <span><i class="bi bi-geo-alt me-1"></i>{{ $listing->location_city }}, {{ $listing->location_state }}{{ $listing->location_country ? ', ' . $listing->location_country : '' }}</span>
                        <span><i class="bi bi-clock me-1"></i>{{ $listing->created_at->diffForHumans() }}</span>
                        <span><i class="bi bi-eye me-1"></i>{{ number_format($listing->view_count) }} views</span>
                        @if($listing->listing_type)
                            <span><i class="bi bi-tag me-1"></i>{{ ucfirst($listing->listing_type) }}</span>
                        @endif
                    </div>

                    <h6 class="fw-bold mb-2">Description</h6>
                    <div class="text-muted" style="white-space:pre-line;line-height:1.7">{{ $listing->description }}</div>

                </div>
            </div>

        </div>

        {{-- Right: seller + contact --}}
        <div class="col-lg-4">

            {{-- Price card (mobile) --}}
            <div class="d-lg-none card border-0 shadow-sm rounded-3 mb-3 p-3">
                <div class="fs-3 fw-bold" style="color:var(--lokabuy-primary)">₹{{ number_format($listing->price, 0) }}</div>
            </div>

            {{-- Contact seller --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Seller Information</h6>

                    <div class="d-flex align-items-center gap-3 mb-4">
                        @if($listing->user->avatar_url)
                            <img src="{{ $listing->user->avatar_url }}" class="rounded-circle"
                                 width="52" height="52" style="object-fit:cover" alt="">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:52px;height:52px">
                                <i class="bi bi-person text-muted fs-4"></i>
                            </div>
                        @endif
                        <div>
                            <div class="fw-semibold">{{ $listing->user->name }}</div>
                            <div class="text-muted small">Member since {{ $listing->user->created_at->format('M Y') }}</div>
                        </div>
                    </div>

                    {{-- T77: Contact seller --}}
                    @guest
                        <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                           class="btn w-100 fw-bold text-white py-2 mb-2"
                           style="background:var(--lokabuy-primary)">
                            <i class="bi bi-chat-dots me-2"></i>Contact Seller
                        </a>
                        <p class="text-muted text-center small mb-0">Login to contact the seller</p>
                    @endguest

                    @auth
                        @if(auth()->id() === $listing->user_id)
                            <a href="{{ route('listings.edit', $listing->slug) }}"
                               class="btn btn-outline-secondary w-100 py-2">
                                <i class="bi bi-pencil me-2"></i>Edit Your Ad
                            </a>
                        @else
                            {{-- Phase 2: in-app messaging. Placeholder for now. --}}
                            <button class="btn w-100 fw-bold text-white py-2 mb-2"
                                    style="background:var(--lokabuy-primary)"
                                    data-bs-toggle="modal" data-bs-target="#contactModal">
                                <i class="bi bi-chat-dots me-2"></i>Contact Seller
                            </button>
                            @if($listing->user->phone)
                            <a href="tel:{{ $listing->user->phone }}"
                               class="btn btn-outline-secondary w-100 py-2">
                                <i class="bi bi-telephone me-2"></i>{{ $listing->user->phone }}
                            </a>
                            @endif
                        @endif
                    @endauth

                </div>
            </div>

            {{-- Report this ad --}}
            @auth
            @if(auth()->id() !== $listing->user_id)
            <div class="text-end mb-3">
                <button class="btn btn-sm btn-link text-muted p-0 text-decoration-none"
                        data-bs-toggle="modal" data-bs-target="#reportModal">
                    <i class="bi bi-flag me-1"></i>Report this ad
                </button>
            </div>
            @endif
            @endauth

            {{-- Category info --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Ad Details</h6>
                    <table class="table table-sm table-borderless mb-0 small">
                        <tr>
                            <td class="text-muted ps-0">Category</td>
                            <td class="fw-semibold">{{ $listing->category->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Posted</td>
                            <td class="fw-semibold">{{ $listing->created_at->format('d M Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Related listings --}}
    @if($related->isNotEmpty())
    <div class="mt-5">
        <h5 class="fw-bold mb-3" style="color:var(--lokabuy-primary)">Related Ads</h5>
        <div class="row g-3">
            @foreach($related as $rel)
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                @include('partials._listing-card', ['listing' => $rel])
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- Contact modal (Phase 2 placeholder) --}}
@auth
@if(auth()->id() !== $listing->user_id)
<div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Contact Seller</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-chat-dots fs-1 mb-3 d-block" style="color:var(--lokabuy-primary)"></i>
                <p class="text-muted">In-app messaging is coming soon. For now, please use the seller's phone number if available.</p>
                @if($listing->user->phone)
                    <a href="tel:{{ $listing->user->phone }}"
                       class="btn fw-bold text-white px-4"
                       style="background:var(--lokabuy-primary)">
                        <i class="bi bi-telephone me-2"></i>{{ $listing->user->phone }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endauth

@endsection

{{-- Report modal --}}
@auth
@if(auth()->id() !== $listing->user_id)
<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true"
     x-data="reportForm()" @shown.bs.modal.window="reset()">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Report this Ad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <div x-show="submitted" x-cloak class="text-center py-3">
                    <i class="bi bi-check-circle fs-1 text-success d-block mb-2"></i>
                    <p class="mb-0">Thank you. Our team will review this report.</p>
                </div>

                <div x-show="!submitted">
                    <p class="text-muted small mb-3">Why are you reporting this ad?</p>

                    <div class="d-grid gap-2">
                        @foreach(['spam' => 'Spam', 'fraud' => 'Fraud / Scam', 'prohibited' => 'Prohibited item', 'duplicate' => 'Duplicate listing', 'other' => 'Other'] as $val => $label)
                        <label class="border rounded-3 px-3 py-2 d-flex align-items-center gap-2 cursor-pointer"
                               :class="reason === '{{ $val }}' ? 'border-danger bg-danger bg-opacity-10' : ''"
                               style="cursor:pointer">
                            <input type="radio" name="reason" value="{{ $val }}" x-model="reason" class="form-check-input mt-0">
                            <span class="small">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>

                    <div x-show="error" x-cloak class="text-danger small mt-2" x-text="error"></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0" x-show="!submitted">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm fw-bold"
                        :disabled="!reason || loading"
                        @click="submit">
                    <span x-show="loading" class="spinner-border spinner-border-sm me-1"></span>
                    Submit Report
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endauth

@push('scripts')
<script>
function reportForm() {
    return {
        reason: '',
        loading: false,
        submitted: false,
        error: '',

        reset() {
            this.reason = '';
            this.loading = false;
            this.submitted = false;
            this.error = '';
        },

        async submit() {
            if (!this.reason) return;
            this.loading = true;
            this.error = '';
            try {
                const res = await fetch('{{ route('listings.report', $listing->slug) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ reason: this.reason }),
                });
                const json = await res.json();
                if (json.success) {
                    this.submitted = true;
                } else {
                    this.error = json.error ?? 'Something went wrong.';
                }
            } catch {
                this.error = 'Could not submit report. Please try again.';
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>
@endpush

@push('styles')
<style>
[x-cloak] { display: none !important; }
</style>
@endpush
