@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">

                    <h4 class="fw-bold mb-1" style="color:var(--lokabuy-primary)">Forgot Password?</h4>
                    <p class="text-muted small mb-4">Enter your email and we'll send you a reset link.</p>

                    @if(session('status'))
                        <div class="alert alert-success small">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Email address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required autofocus placeholder="you@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn w-100 fw-bold text-white py-2"
                                style="background:var(--lokabuy-primary)">
                            Send Reset Link
                        </button>
                    </form>

                    <p class="text-center small mt-3 mb-0">
                        <a href="{{ route('login') }}" style="color:var(--lokabuy-primary)">
                            <i class="bi bi-arrow-left me-1"></i>Back to Login
                        </a>
                    </p>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
