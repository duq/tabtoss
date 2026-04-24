<div class="relative flex-none" x-data="{ menuOpen: false }" @click.outside="menuOpen = false">
    <button
        type="button"
        class="inline-flex items-center gap-2 rounded-lg px-2 py-1.5 text-neutral-600 hover:bg-neutral-100"
        @click="menuOpen = ! menuOpen"
    >
        <span class="inline-flex size-8 items-center justify-center rounded-lg bg-primary-700 text-sm font-semibold text-white capitalize">
            {{ substr(auth()->user()->name, 0, 1) }}
        </span>
        <svg viewBox="0 0 24 24" class="size-4 text-neutral-500" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="m6 9 6 6 6-6" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
    </button>

    <div
        x-show="menuOpen"
        x-cloak
        class="absolute right-0 z-30 mt-2 w-56 rounded-xl border border-neutral-200 bg-white p-1 shadow-sm"
    >
        @if (auth()->user()->isAdmin())
            <a href="{{ route('filament.admin.pages.dashboard') }}" class="flex rounded-lg px-3 py-2 text-sm text-neutral-700 hover:bg-neutral-100">
                {{ __('Admin Panel') }}
            </a>
        @endif
        <a href="{{ route('filament.dashboard.pages.dashboard') }}" class="flex rounded-lg px-3 py-2 text-sm text-neutral-700 hover:bg-neutral-100">
            {{ __('Dashboard') }}
        </a>
        <div class="my-1 border-t border-neutral-200"></div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full rounded-lg px-3 py-2 text-left text-sm text-neutral-700 hover:bg-neutral-100">
                {{ __('Logout') }}
            </button>
        </form>
    </div>
</div>

