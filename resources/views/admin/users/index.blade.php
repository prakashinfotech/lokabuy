@extends('layouts.admin')

@section('title', 'Users')

@section('content')

{{-- Search --}}
<form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
    <div class="input-group" style="max-width:360px">
        <input type="text" name="q" class="form-control form-control-sm"
               placeholder="Search by name or email…" value="{{ request('q') }}">
        <button class="btn btn-sm btn-outline-secondary" type="submit">
            <i class="bi bi-search"></i>
        </button>
        @if(request('q'))
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
        @endif
    </div>
</form>

<div class="card border-0 shadow-sm rounded-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="ps-3">
                        <div class="d-flex align-items-center gap-2">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" class="rounded-circle"
                                     width="34" height="34" style="object-fit:cover;flex-shrink:0">
                            @else
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:34px;height:34px">
                                    <i class="bi bi-person text-muted small"></i>
                                </div>
                            @endif
                            <div>
                                <div class="fw-semibold small">{{ $user->name }}</div>
                                <div class="text-muted" style="font-size:.72rem">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>

                    <td>
                        <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : ($user->role === 'moderator' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>

                    <td>
                        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>

                    <td class="small text-muted">{{ $user->created_at->format('d M Y') }}</td>

                    <td class="text-end pe-3">
                        @if($user->id !== auth()->id() && ! $user->isAdmin())
                            @if($user->is_active)
                                <form method="POST"
                                      action="{{ route('admin.users.deactivate', $user->id) }}"
                                      onsubmit="return confirm('Deactivate {{ addslashes($user->name) }}?')">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-danger">Deactivate</button>
                                </form>
                            @else
                                <form method="POST"
                                      action="{{ route('admin.users.activate', $user->id) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-success">Activate</button>
                                </form>
                            @endif
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    @include('partials._pagination', ['paginator' => $users])
</div>

@endsection
