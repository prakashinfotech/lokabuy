@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">

                    <h4 class="fw-bold mb-1" style="color:var(--lokabuy-primary)">Create an Account</h4>
                    <p class="text-muted small mb-4">Join Lokabuy to buy and sell anything.</p>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Full Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required autofocus placeholder="John Doe">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Email address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required placeholder="you@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Phone <span class="text-muted">(optional)</span></label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone') }}" placeholder="+91 98765 43210">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" x-data="{ show: false }">
                            <label class="form-label fw-semibold small">Password</label>
                            <div class="input-group">
                                <input :type="show ? 'text' : 'password'" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       required placeholder="Min. 8 characters">
                                <button type="button" class="btn btn-outline-secondary" @click="show = !show">
                                    <i :class="show ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Confirm Password</label>
                            <input type="password" name="password_confirmation"
                                   class="form-control" required placeholder="Repeat password">
                        </div>

                        <button type="submit" class="btn w-100 fw-bold text-white py-2"
                                style="background:var(--lokabuy-primary)">
                            Create Account
                        </button>
                    </form>

                    <hr class="my-4">

                    <p class="text-center small mb-0">
                        Already have an account?
                        <a href="{{ route('login') }}" class="fw-semibold" style="color:var(--lokabuy-primary)">Login</a>
                    </p>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
