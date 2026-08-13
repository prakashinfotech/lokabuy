<nav class="navbar navbar-lokabuy navbar-expand-lg sticky-top" x-data="{ open: false }">
    <div class="container">

        {{-- Logo --}}
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <img src="{{ asset('images/lokabuy.svg') }}" alt="Lokabuy Logo" style="height: 36px; width: auto;" class="img-fluid">
        </a>

        {{-- Search bar with autocomplete --}}
        <div class="search-bar-wrap mx-3 d-none d-lg-flex"
             x-data="searchAutocomplete()"
             @click.away="suggestions = []">
            <form action="{{ route('listings.index') }}" method="GET" class="w-100 position-relative">
                <div class="input-group">
                    <input
                        type="text"
                        name="q"
                        class="form-control"
                        placeholder="Search for cars, mobile phones and more..."
                        value="{{ request('q') }}"
                        autocomplete="off"
                        x-model="query"
                        @input.debounce.300ms="fetchSuggestions"
                        @keydown.escape="suggestions = []"
                        @keydown.arrow-down.prevent="highlightNext"
                        @keydown.arrow-up.prevent="highlightPrev"
                        @keydown.enter.prevent="selectHighlighted"
                    >
                    <button type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>

                {{-- Dropdown --}}
                <ul class="list-unstyled mb-0 position-absolute w-100 bg-white border rounded-3 shadow-sm"
                    style="top:100%;left:0;z-index:1000;max-height:300px;overflow-y:auto"
                    x-show="suggestions.length > 0" x-cloak>
                    <template x-for="(item, i) in suggestions" :key="i">
                        <li>
                            <a :href="'/listings/' + item.slug"
                               class="d-block px-3 py-2 text-decoration-none text-dark small"
                               :class="i === highlighted ? 'bg-light' : ''"
                               @mouseenter="highlighted = i"
                               x-text="item.title"></a>
                        </li>
                    </template>
                </ul>
            </form>
        </div>

        {{-- Mobile toggle --}}
        <button class="navbar-toggler border-0" type="button" @click="open = !open">
            <i class="bi bi-list text-white fs-4"></i>
        </button>

        {{-- Right nav --}}
        <div class="collapse navbar-collapse" :class="open ? 'show' : ''" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">

                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                @endguest

                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-1"
                           href="#"
                           data-bs-toggle="dropdown"
                           aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                            <span>{{ Str::limit(auth()->user()->name, 12) }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end mt-1 shadow-sm">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item" href="{{ route('listings.my') }}"><i class="bi bi-megaphone me-2"></i>My Ads</a></li>
<li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-gear me-2"></i>Profile</a></li>
                            @if(auth()->user()->isModerator())
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-shield-check me-2"></i>Admin Panel</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth

                <li class="nav-item ms-lg-2">
                    <a class="btn btn-sell" href="{{ route('listings.create') }}">
                        <i class="bi bi-plus-lg me-1"></i>SELL
                    </a>
                </li>

            </ul>
        </div>

    </div>
</nav>

{{-- Mobile search --}}
<div class="bg-white border-bottom py-2 d-lg-none">
    <div class="container">
        <form action="{{ route('listings.index') }}" method="GET">
            <div class="input-group">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Search..." value="{{ request('q') }}">
                <button class="btn btn-sm" style="background:#f26b38;color:#fff;" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>
