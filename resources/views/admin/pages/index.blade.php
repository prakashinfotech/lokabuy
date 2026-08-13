@extends('layouts.admin')

@section('title', 'Pages')

@section('content')


<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Static Pages</h5>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Title</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pages as $page)
                <tr>
                    <td class="ps-3 fw-semibold">{{ $page->title }}</td>
                    <td class="text-muted small">/pages/{{ $page->slug }}</td>
                    <td>
                        <span class="badge {{ $page->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $page->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="small text-muted">{{ $page->updated_at->format('d M Y, H:i') }}</td>
                    <td class="text-end pe-3">
                        <a href="{{ route('admin.pages.edit', $page) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
