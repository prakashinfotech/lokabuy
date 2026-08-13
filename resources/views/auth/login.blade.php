@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">

                    <h4 class="fw-bold mb-1" style="color:var(--lokabuy-primary)">Login to Lokabuy</h4>
                    <p class="text-muted small mb-4">Welcome back! Please enter your credentials.</p>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Email address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required autofocus placeholder="you@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" x-data="{ show: false }">
                            <label class="form-label fw-semibold small">Password</label>
                            <div class="input-group">
                                <input :type="show ? 'text' : 'password'" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       required placeholder="••••••••">
                                <button type="button" class="btn btn-outline-secondary" @click="show = !show">
                                    <i :class="show ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label small" for="remember">Remember me</label>
                            </div>
                            <a href="{{ route('password.request') }}" class="small text-decoration-underline" style="color:var(--lokabuy-primary)">
                                Forgot password?
                            </a>
                        </div>

                        <button type="submit" class="btn w-100 fw-bold text-white py-2"
                                style="background:var(--lokabuy-primary)">
                            Login
                        </button>
                    </form>

                    <hr class="my-4">

                    <p class="text-center small mb-0">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="fw-semibold" style="color:var(--lokabuy-primary)">Register</a>
                    </p>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
