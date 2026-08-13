@extends('layouts.app')

@section('title', 'My Favourites')

@section('content')
<div class="container py-4">

    <h4 class="fw-bold mb-4" style="color:var(--lokabuy-primary)">My Favourites</h4>


    @if($favorites->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-heart fs-1 text-muted"></i>
            <p class="text-muted mt-3">You haven't saved any ads yet.</p>
            <a href="{{ route('listings.index') }}" class="btn fw-bold text-white px-4"
               style="background:var(--lokabuy-primary)">Browse Ads</a>
        </div>
    @else
        <div class="row g-3">
            @foreach($favorites as $fav)
                @if($fav->listing)
                <div class="col-sm-6 col-md-4 col-lg-3" x-data="{ removed: false }">
                    <div x-show="!removed" class="position-relative">
                        @include('partials._listing-card', ['listing' => $fav->listing])

                        {{-- Remove from favourites --}}
                        <button
                            class="btn btn-sm btn-light position-absolute top-0 end-0 m-2 rounded-circle shadow-sm"
                            title="Remove from favourites"
                            @click.prevent="
                                fetch('/api/v1/favorites/{{ $fav->listing->id }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        'Accept': 'application/json',
                                    }
                                }).then(() => removed = true)
                            ">
                            <i class="bi bi-heart-fill text-danger"></i>
                        </button>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <div class="mt-4">
            @include('partials._pagination', ['paginator' => $favorites])
        </div>
    @endif

</div>
@endsection
