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

<div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

    {{-- Backdrop — mobile only, closes the drawer on tap --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
         class="fixed inset-0 bg-ink/50 z-30 lg:hidden" x-transition.opacity></div>

    {{-- Sidebar: off-canvas drawer on mobile, static column from lg: up --}}
    <aside
        class="fixed inset-y-0 left-0 z-40 w-64 bg-ink text-paper flex flex-col
               transform transition-transform duration-200 ease-in-out
               lg:static lg:translate-x-0 lg:w-60 lg:shrink-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="px-6 py-5 border-b border-white/10 flex items-center justify-between">
            <div>
                <a href="{{ route('dashboard') }}" class="font-display text-lg font-semibold">GoldLoan</a>
                <p class="text-xs text-paper/60 mt-0.5 truncate">{{ auth()->user()->tenant->business_name }}</p>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden text-paper/70 hover:text-paper" aria-label="Close menu">
                ✕
            </button>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
            <a href="{{ route('dashboard') }}" @click="sidebarOpen = false"
               class="block px-3 py-2 rounded-sm {{ request()->routeIs('dashboard') ? 'bg-brass text-ink font-medium' : 'text-paper/80 hover:bg-white/10' }}">
                Dashboard
            </a>
            <a href="{{ route('customers.index') }}" @click="sidebarOpen = false"
               class="block px-3 py-2 rounded-sm {{ request()->routeIs('customers.*') ? 'bg-brass text-ink font-medium' : 'text-paper/80 hover:bg-white/10' }}">
                Customers
            </a>
            <a href="{{ route('loans.index') }}" @click="sidebarOpen = false"
               class="block px-3 py-2 rounded-sm {{ request()->routeIs('loans.*') ? 'bg-brass text-ink font-medium' : 'text-paper/80 hover:bg-white/10' }}">
                Loans
            </a>
            <a href="{{ route('billing.show') }}" @click="sidebarOpen = false"
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

    <div class="flex-1 flex flex-col min-w-0">
        @unless (auth()->user()->tenant->isSubscriptionActive())
            <div class="bg-alert text-paper text-sm px-4 sm:px-8 py-2 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                <span>Your subscription has expired or hasn't started yet.</span>
                <a href="{{ route('billing.show') }}" class="underline font-medium">Renew now</a>
            </div>
        @endunless

        <header class="border-b border-paper-line px-4 sm:px-8 py-4 flex items-center justify-between bg-white">
            <div class="flex items-center gap-3 min-w-0">
                <button @click="sidebarOpen = true" class="lg:hidden text-ink shrink-0" aria-label="Open menu">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 6h18M3 12h18M3 18h18" stroke-linecap="round"/>
                    </svg>
                </button>
                <h1 class="font-display text-lg sm:text-xl font-semibold text-ink truncate">@yield('title', 'Dashboard')</h1>
            </div>
            <span class="hidden sm:block text-sm text-ink-soft shrink-0">{{ auth()->user()->name }} · {{ auth()->user()->role?->name }}</span>
        </header>

        <main class="flex-1 px-4 sm:px-8 py-6 sm:py-8 min-w-0">
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
