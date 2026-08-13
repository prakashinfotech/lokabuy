<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — @yield('title', 'Dashboard') | Lokabuy</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>

<div class="d-flex">

    {{-- Sidebar --}}
    <aside class="admin-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">Lokabuy Admin</a>

        <nav class="mt-2">
            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('admin.listings.index') }}"
               class="nav-link {{ request()->routeIs('admin.listings.*') ? 'active' : '' }}">
                <i class="bi bi-list-ul"></i> Listings
                @php $pendingCount = \App\Models\Listing::pending()->count(); @endphp
                @if($pendingCount > 0)
                    <span class="badge bg-warning text-dark ms-auto">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.reports.index') }}"
               class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="bi bi-flag"></i> Reports
                @php $reportsCount = \App\Models\Report::pending()->count(); @endphp
                @if($reportsCount > 0)
                    <span class="badge bg-danger ms-auto">{{ $reportsCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Users
            </a>
            <a href="{{ route('admin.categories.index') }}"
               class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="bi bi-grid"></i> Categories
            </a>
            <a href="{{ route('admin.pages.index') }}"
               class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                <i class="bi bi-file-text"></i> Pages
            </a>

            <hr class="border-secondary my-2">

            <a href="{{ route('home') }}" class="nav-link">
                <i class="bi bi-box-arrow-left"></i> Back to Site
            </a>

            <form method="POST" action="{{ route('logout') }}" class="px-3 mt-2">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light w-100">
                    <i class="bi bi-power"></i> Logout
                </button>
            </form>
        </nav>
    </aside>

    {{-- Main content --}}
    <div class="admin-content">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">@yield('title', 'Dashboard')</h5>
            <span class="text-muted small">
                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
            </span>
        </div>

        @include('partials._alert')

        @yield('content')
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js" defer></script>
@stack('scripts')
</body>
</html>
