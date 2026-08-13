@extends('layouts.guest')

@section('title', 'Verify Your Email')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-5 text-center">

                    <i class="bi bi-envelope-check fs-1 mb-3 d-block" style="color:var(--lokabuy-primary)"></i>

                    <h4 class="fw-bold mb-2">Verify your email address</h4>
                    <p class="text-muted mb-4">
                        We sent a verification link to <strong>{{ auth()->user()->email }}</strong>.
                        Click the link in that email to activate your account.
                    </p>

                    @if(session('status') === 'Verification link sent!')
                        <div class="alert alert-success py-2 small">
                            A new verification link has been sent to your email.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn fw-bold text-white px-4"
                                style="background:var(--lokabuy-primary)">
                            Resend verification email
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-link text-muted small p-0">
                            Log out
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
