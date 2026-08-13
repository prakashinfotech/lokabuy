@extends('layouts.admin')

@section('title', 'Categories')

@section('content')

<div class="row g-4">

    {{-- Category list --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Category</th>
                            <th class="text-center">Subcategories</th>
                            <th class="text-center">Active Ads</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr x-data="{ editing: false }">
                            <td class="ps-3">
                                <div x-show="!editing" class="d-flex align-items-center gap-2">
                                    @if($category->icon && (str_starts_with($category->icon, '/') || str_starts_with($category->icon, 'http')))
                                        <img src="{{ $category->icon }}" width="20" height="20"
                                             style="object-fit:contain" alt="">
                                    @else
                                        <i class="{{ $category->icon ?? 'bi bi-tag' }} text-muted"></i>
                                    @endif
                                    <span class="fw-semibold small">{{ $category->name }}</span>
                                </div>

                                {{-- Inline edit form --}}
                                <div x-show="editing" x-cloak>
                                    <form method="POST"
                                          action="{{ route('admin.categories.update', $category->id) }}"
                                          enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="d-flex gap-2 align-items-center flex-wrap">
                                            <input type="text" name="name" class="form-control form-control-sm"
                                                   style="max-width:160px" value="{{ $category->name }}" required>
                                            <div>
                                                @if($category->icon && (str_starts_with($category->icon, '/') || str_starts_with($category->icon, 'http')))
                                                    <img src="{{ $category->icon }}" width="24" height="24"
                                                         class="me-1 rounded" style="object-fit:contain" alt="">
                                                @endif
                                                <input type="file" name="icon"
                                                       class="form-control form-control-sm d-inline-block"
                                                       style="max-width:180px"
                                                       accept=".svg,.png">
                                                <div class="form-text" style="font-size:.7rem">SVG or PNG, max 100 KB</div>
                                            </div>
                                            <button class="btn btn-sm btn-success">Save</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    @click="editing = false">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </td>

                            <td class="text-center small">
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#subModal-{{ $category->id }}">
                                    <i class="bi bi-list-nested me-1"></i>{{ $category->subcategories->count() }}
                                </button>
                            </td>
                            <td class="text-center small">{{ number_format($category->listings_count) }}</td>

                            <td class="text-center">
                                <form method="POST"
                                      action="{{ route('admin.categories.update', $category->id) }}">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="is_active" value="{{ $category->is_active ? '0' : '1' }}">
                                    <button class="btn btn-sm {{ $category->is_active ? 'btn-success' : 'btn-outline-secondary' }}"
                                            title="{{ $category->is_active ? 'Click to deactivate' : 'Click to activate' }}">
                                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>

                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-outline-secondary"
                                        @click="editing = !editing" x-show="!editing">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add new category --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Add Category</h6>
                <form method="POST" action="{{ route('admin.categories.store') }}"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm
                               @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required maxlength="100">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Icon</label>
                        <input type="file" name="icon" class="form-control form-control-sm"
                               accept=".svg,.png">
                        <div class="form-text small">SVG or PNG, max 100 KB</div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control form-control-sm"
                               value="{{ old('sort_order', 0) }}" min="0">
                    </div>
                    <button type="submit" class="btn btn-sm fw-bold text-white w-100"
                            style="background:var(--lokabuy-primary)">
                        <i class="bi bi-plus-lg me-1"></i>Add Category
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

{{-- Subcategory modals --}}
@foreach($categories as $category)
<div class="modal fade" id="subModal-{{ $category->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-3">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">{{ $category->name }} — Subcategories</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                @if($category->subcategories->isEmpty())
                    <p class="text-muted small text-center py-3">No subcategories yet.</p>
                @else
                <table class="table table-sm align-middle mb-0">
                    <tbody>
                        @foreach($category->subcategories as $sub)
                        <tr x-data="{ editing: false }">
                            <td>
                                <div x-show="!editing" class="small fw-semibold">{{ $sub->name }}</div>
                                <div x-show="editing" x-cloak>
                                    <form method="POST"
                                          action="{{ route('admin.categories.subcategories.update', [$category->id, $sub->id]) }}">
                                        @csrf @method('PUT')
                                        <div class="d-flex gap-2">
                                            <input type="text" name="name" class="form-control form-control-sm"
                                                   value="{{ $sub->name }}" required maxlength="100">
                                            <button class="btn btn-sm btn-success flex-shrink-0">Save</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary flex-shrink-0"
                                                    @click="editing = false">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                            <td class="text-end" style="white-space:nowrap">
                                <form class="d-inline" method="POST"
                                      action="{{ route('admin.categories.subcategories.toggle', [$category->id, $sub->id]) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm {{ $sub->is_active ? 'btn-success' : 'btn-outline-secondary' }}"
                                            title="{{ $sub->is_active ? 'Click to deactivate' : 'Click to activate' }}">
                                        {{ $sub->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                                <button class="btn btn-sm btn-outline-secondary ms-1"
                                        @click="editing = true" x-show="!editing">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                <hr class="my-3">

                <h6 class="fw-semibold mb-2 small">Add Subcategory</h6>
                <form method="POST"
                      action="{{ route('admin.categories.subcategories.store', $category->id) }}">
                    @csrf
                    <div class="d-flex gap-2">
                        <input type="text" name="name" class="form-control form-control-sm"
                               placeholder="Subcategory name" required maxlength="100">
                        <button class="btn btn-sm fw-bold text-white flex-shrink-0"
                                style="background:var(--lokabuy-primary)">
                            <i class="bi bi-plus-lg"></i> Add
                        </button>
                    </div>
                </form>

            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary btn-sm"
                        data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@push('styles')
<style>[x-cloak] { display: none !important; }</style>
@endpush

@endsection
