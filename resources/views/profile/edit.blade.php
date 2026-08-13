@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <h4 class="fw-bold mb-4" style="color:var(--lokabuy-primary)">Edit Profile</h4>


            {{-- Avatar --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Profile Photo</h6>

                    <div class="d-flex align-items-center gap-4">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" class="rounded-circle"
                                 width="80" height="80" style="object-fit:cover" id="avatar-preview">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                 style="width:80px;height:80px" id="avatar-placeholder">
                                <i class="bi bi-person text-muted fs-2"></i>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.avatar') }}"
                              enctype="multipart/form-data" id="avatar-form">
                            @csrf
                            @method('POST')
                            <label class="btn btn-outline-secondary btn-sm mb-1" for="avatar-input">
                                <i class="bi bi-upload me-1"></i>Upload Photo
                            </label>
                            <input type="file" id="avatar-input" name="avatar" class="d-none"
                                   accept="image/jpeg,image/png,image/webp"
                                   onchange="document.getElementById('avatar-form').submit()">
                            <div class="text-muted small">JPEG, PNG or WebP, max 2 MB</div>
                            @error('avatar')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </form>
                    </div>
                </div>
            </div>

            {{-- Profile details --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Personal Details</h6>

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required maxlength="100">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Phone <span class="text-muted">(optional)</span></label>
                            <input type="text" name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $user->phone) }}" maxlength="20"
                                   placeholder="+91 98765 43210">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="email_notifications" id="email_notifications" value="1"
                                       {{ old('email_notifications', $user->email_notifications) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="email_notifications">
                                    Receive email notifications (listing approvals, expiry warnings)
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn fw-bold text-white px-4"
                                style="background:var(--lokabuy-primary)">
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>

            {{-- Change password --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-1">Password</h6>
                    <p class="text-muted small mb-3">To change your password, use the forgot password flow.</p>
                    <a href="{{ route('password.request') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-lock me-1"></i>Change Password
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
