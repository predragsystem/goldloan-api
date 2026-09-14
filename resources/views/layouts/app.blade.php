<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — GoldLoan</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=zilla-slab:500,600,700|ibm-plex-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-paper text-ink font-sans antialiased">

<div class="flex min-h-screen">

    <aside class="w-60 shrink-0 bg-ink text-paper flex flex-col">
        <div class="px-6 py-5 border-b border-white/10">
            <a href="{{ route('dashboard') }}" class="font-display text-lg font-semibold">GoldLoan</a>
            <p class="text-xs text-paper/60 mt-0.5 truncate">{{ auth()->user()->tenant->business_name }}</p>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
            <a href="{{ route('dashboard') }}"
               class="block px-3 py-2 rounded-sm {{ request()->routeIs('dashboard') ? 'bg-brass text-ink font-medium' : 'text-paper/80 hover:bg-white/10' }}">
                Dashboard
            </a>
            <a href="{{ route('customers.index') }}"
               class="block px-3 py-2 rounded-sm {{ request()->routeIs('customers.*') ? 'bg-brass text-ink font-medium' : 'text-paper/80 hover:bg-white/10' }}">
                Customers
            </a>
            <a href="{{ route('loans.index') }}"
               class="block px-3 py-2 rounded-sm {{ request()->routeIs('loans.*') ? 'bg-brass text-ink font-medium' : 'text-paper/80 hover:bg-white/10' }}">
                Loans
            </a>
            <a href="{{ route('billing.show') }}"
               class="block px-3 py-2 rounded-sm {{ request()->routeIs('billing.*') ? 'bg-brass text-ink font-medium' : 'text-paper/80 hover:bg-white/10' }}">
                Billing
            </a>
        </nav>

        <div class="px-3 py-4 border-t border-white/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-sm text-sm text-paper/80 hover:bg-white/10">
                    Log out
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col">
        @unless (auth()->user()->tenant->isSubscriptionActive())
            <div class="bg-alert text-paper text-sm px-6 py-2 flex items-center justify-between">
                <span>Your subscription has expired or hasn't started yet.</span>
                <a href="{{ route('billing.show') }}" class="underline font-medium">Renew now</a>
            </div>
        @endunless

        <header class="border-b border-paper-line px-8 py-4 flex items-center justify-between bg-white">
            <h1 class="font-display text-xl font-semibold text-ink">@yield('title', 'Dashboard')</h1>
            <span class="text-sm text-ink-soft">{{ auth()->user()->name }} · {{ auth()->user()->role?->name }}</span>
        </header>

        <main class="flex-1 px-8 py-8">
            @if (session('status'))
                <div class="mb-6 border border-success/30 bg-success/10 text-success text-sm rounded-sm p-4">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-6 border border-alert/30 bg-alert/5 text-alert text-sm rounded-sm p-4">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
