<div class="card border-0 shadow-sm rounded-3 h-100 listing-card">
    <a href="{{ route('listings.show', $listing->slug) }}" class="text-decoration-none">

        {{-- Thumbnail --}}
        <div class="position-relative" style="height:160px;overflow:hidden">
            @if($listing->images->isNotEmpty())
                <img src="{{ $listing->images->first()->url }}"
                     class="w-100 h-100 rounded-top-3"
                     style="object-fit:cover"
                     alt="{{ $listing->title }}"
                     loading="lazy">
            @else
                <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center rounded-top-3">
                    <i class="bi bi-image text-muted fs-2"></i>
                </div>
            @endif

            @if($listing->is_featured)
                <span class="position-absolute top-0 start-0 m-2 badge"
                      style="background:var(--lokabuy-orange)">Featured</span>
            @endif
        </div>

        {{-- Body --}}
        <div class="card-body p-3">
            <div class="fw-bold fs-6 mb-1" style="color:var(--lokabuy-primary)">
                ₹{{ number_format($listing->price, 0) }}
            </div>
            <div class="text-dark fw-semibold small text-truncate" title="{{ $listing->title }}">
                {{ $listing->title }}
            </div>
            <div class="text-muted d-flex justify-content-between mt-2" style="font-size:0.75rem">
                <span><i class="bi bi-geo-alt me-1"></i>{{ $listing->location_city }}</span>
                <span>{{ $listing->created_at->diffForHumans() }}</span>
            </div>
        </div>

    </a>
</div>
