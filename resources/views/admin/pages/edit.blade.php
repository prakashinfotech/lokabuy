@extends('layouts.admin')

@section('title', 'Edit Page â€” ' . $page->title)

@section('content')


<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.pages.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="fw-bold mb-0">Edit: {{ $page->title }}</h5>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.pages.update', $page) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold small">Title <span class="text-danger">*</span></label>
                <input type="text" name="title"
                       class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title', $page->title) }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">
                    Content <span class="text-danger">*</span>
                    <span class="text-muted fw-normal">(HTML supported)</span>
                </label>
                <textarea name="content" rows="20"
                          class="form-control font-monospace @error('content') is-invalid @enderror"
                          required>{{ old('content', $page->content) }}</textarea>
                @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                           value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold small" for="is_active">Active (visible to public)</label>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary fw-bold px-4">
                    <i class="bi bi-check-lg me-1"></i>Save Changes
                </button>
                <a href="{{ route('pages.show', $page) }}" target="_blank"
                   class="btn btn-outline-secondary">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Preview
                </a>
            </div>

        </form>
    </div>
</div>

@endsection
