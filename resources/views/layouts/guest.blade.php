<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'GoldLoan') — Girvi loan management</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=zilla-slab:500,600,700|ibm-plex-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-paper text-ink font-sans antialiased">

    <header class="border-b border-paper-line">
        <div class="mx-auto max-w-6xl px-6 py-5 flex items-center justify-between">
            <a href="{{ url('/') }}" class="font-display text-xl font-semibold tracking-tight text-ink">
                GoldLoan
            </a>
            <nav class="flex items-center gap-6 text-sm">
                <a href="{{ url('/#pricing') }}" class="text-ink-soft hover:text-ink">Pricing</a>
                <a href="{{ route('login') }}" class="text-ink-soft hover:text-ink">Log in</a>
                <a href="{{ route('signup') }}"
                   class="bg-ink text-paper px-4 py-2 rounded-sm text-sm font-medium hover:bg-brass-dark transition-colors">
                    Get started
                </a>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="border-t border-paper-line mt-24">
        <div class="mx-auto max-w-6xl px-6 py-10 flex flex-col sm:flex-row justify-between gap-4 text-sm text-ink-soft">
            <p>&copy; {{ date('Y') }} GoldLoan. A product of PreDrag System LLP.</p>
            <div class="flex gap-6">
                <a href="{{ url('/#pricing') }}" class="hover:text-ink">Pricing</a>
                <a href="{{ route('login') }}" class="hover:text-ink">Log in</a>
            </div>
        </div>
    </footer>

</body>
</html>
