<footer class="site-footer mt-5">
    <div class="container">
        <div class="row g-4">

            <div class="col-lg-3">
                <div class="mb-2">
                    <img src="{{ asset('images/lokabuy.svg') }}" alt="Lokabuy Logo" style="height: 32px; width: auto;" class="img-fluid">
                </div>
                <p class="small">India's leading platform for buying and selling used goods.</p>
            </div>

            <div class="col-lg-2 col-6">
                <p class="fw-semibold text-white mb-2">Popular</p>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('listings.index', ['category' => 'vehicles']) }}">Vehicles</a></li>
                    <li><a href="{{ route('listings.index', ['category' => 'real-estate']) }}">Real Estate</a></li>
                    <li><a href="{{ route('listings.index', ['category' => 'electronics']) }}">Electronics</a></li>
                    <li><a href="{{ route('listings.index', ['category' => 'jobs']) }}">Jobs</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-6">
                <p class="fw-semibold text-white mb-2">Account</p>
                <ul class="list-unstyled small">
                    @guest
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('register') }}">Register</a></li>
                    @endguest
                    @auth
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('listings.create') }}">Post an Ad</a></li>
                    @endauth
                </ul>
            </div>

            <div class="col-lg-2 col-6">
                <p class="fw-semibold text-white mb-2">Help</p>
                <ul class="list-unstyled small">
                    <li><a href="mailto:support@lokabuy.com">Contact Us</a></li>
                    <li><a href="{{ route('pages.show', 'privacy-policy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('pages.show', 'terms-of-use') }}">Terms of Use</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-6">
                <p class="fw-semibold text-white mb-2">Follow Us</p>
                <div class="d-flex gap-3 fs-5">
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-twitter-x"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

        </div>

        <hr class="border-secondary mt-4">
        <p class="text-center small mb-0">&copy; {{ date('Y') }} Lokabuy. All rights reserved.</p>
    </div>
</footer>
