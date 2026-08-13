@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 text-center p-3">
            <div class="fs-2 fw-bold" style="color:var(--lokabuy-primary)">{{ number_format($stats['total_users']) }}</div>
            <div class="text-muted small mt-1">Total Users</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 text-center p-3">
            <div class="fs-2 fw-bold text-success">{{ number_format($stats['active_listings']) }}</div>
            <div class="text-muted small mt-1">Active Listings</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 text-center p-3">
            <a href="{{ route('admin.listings.index', ['status' => 'pending']) }}" class="text-decoration-none">
                <div class="fs-2 fw-bold text-warning">{{ number_format($stats['pending_listings']) }}</div>
                <div class="text-muted small mt-1">Pending Approval</div>
            </a>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 text-center p-3">
            <a href="{{ route('admin.reports.index') }}" class="text-decoration-none">
                <div class="fs-2 fw-bold text-danger">{{ number_format($stats['pending_reports']) }}</div>
                <div class="text-muted small mt-1">Pending Reports</div>
            </a>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
            <div class="fs-4 fw-bold text-info">{{ number_format($stats['new_users_last_7days']) }}</div>
            <div class="text-muted small">New Users (last 7 days)</div>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2 flex-wrap">
    <a href="{{ route('admin.listings.index', ['status' => 'pending']) }}"
       class="btn fw-bold text-white px-4" style="background:var(--lokabuy-primary)">
        <i class="bi bi-check2-circle me-1"></i>Review Pending Listings
    </a>
    <a href="{{ route('admin.reports.index') }}"
       class="btn btn-outline-danger px-4">
        <i class="bi bi-flag me-1"></i>Review Reports
    </a>
</div>

@endsection
