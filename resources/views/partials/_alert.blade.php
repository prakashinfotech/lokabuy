@if(session('success') || session('error') || session('warning') || session('info'))
    <div class="container mt-3">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ session('warning') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                <span>{{ session('info') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

    </div>
@endif
