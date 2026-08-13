<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Lokabuy')</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>

    @include('partials._navbar')

    @include('partials._alert')

    <main>
        @yield('content')
    </main>

    @include('partials._footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function searchAutocomplete() {
        return {
            query: '',
            suggestions: [],
            highlighted: -1,

            async fetchSuggestions() {
                this.highlighted = -1;
                if (this.query.length < 2) { this.suggestions = []; return; }
                try {
                    const res = await fetch('/api/v1/listings/autocomplete?q=' + encodeURIComponent(this.query));
                    const json = await res.json();
                    this.suggestions = json.data ?? [];
                } catch { this.suggestions = []; }
            },

            highlightNext() {
                if (this.highlighted < this.suggestions.length - 1) this.highlighted++;
            },
            highlightPrev() {
                if (this.highlighted > 0) this.highlighted--;
            },
            selectHighlighted() {
                if (this.highlighted >= 0 && this.suggestions[this.highlighted]) {
                    window.location.href = '/listings/' + this.suggestions[this.highlighted].slug;
                }
            },
        };
    }
    </script>
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js" defer></script>
</body>
</html>
