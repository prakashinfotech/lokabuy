@extends('layouts.admin')

@section('title', 'Reports')

@section('content')

@if($reports->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="bi bi-flag fs-1 d-block mb-2"></i>
        No pending reports. All clear!
    </div>
@else

<div class="card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Listing</th>
                    <th>Reporter</th>
                    <th>Reason</th>
                    <th>Reported</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $report)
                <tr>
                    <td class="ps-3" style="max-width:220px">
                        @if($report->listing)
                            <div class="d-flex align-items-center gap-2">
                                @if($report->listing->images->isNotEmpty())
                                    <img src="{{ $report->listing->images->first()->url }}"
                                         class="rounded" width="44" height="34"
                                         style="object-fit:cover;flex-shrink:0">
                                @endif
                                <div class="min-w-0">
                                    <div class="fw-semibold small text-truncate" style="max-width:150px">
                                        {{ $report->listing->title }}
                                    </div>
                                    <div class="text-muted" style="font-size:.72rem">
                                        ₹{{ number_format($report->listing->price, 0) }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <span class="text-muted small fst-italic">Listing deleted</span>
                        @endif
                    </td>

                    <td class="small">{{ $report->reporter->name ?? '—' }}</td>

                    <td>
                        <span class="badge bg-secondary">
                            {{ ucfirst($report->reason) }}
                        </span>
                    </td>

                    <td class="small text-muted">
                        {{ $report->created_at->diffForHumans() }}
                    </td>

                    <td class="text-end pe-3">
                        <div class="d-flex gap-1 justify-content-end">

                            @if($report->listing)
                            {{-- Remove listing + resolve --}}
                            <form method="POST"
                                  action="{{ route('admin.reports.action', $report->id) }}"
                                  onsubmit="return confirm('Remove this listing and resolve the report?')">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash me-1"></i>Remove
                                </button>
                            </form>
                            @endif

                            {{-- Dismiss --}}
                            <form method="POST"
                                  action="{{ route('admin.reports.dismiss', $report->id) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-secondary">
                                    Dismiss
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    @include('partials._pagination', ['paginator' => $reports])
</div>

@endif

@endsection
