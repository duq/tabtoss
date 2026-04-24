<nav class="relative border-b border-neutral-300 bg-neutral-100 text-neutral-800">
    <livewire:announcement.view />
    <div class="mx-auto flex h-14 max-w-(--breakpoint-xl) items-center justify-between px-4">
        <div class="flex items-center gap-3">
            <div class="relative lg:hidden" x-data="{ open: false }" @click.outside="open = false">
                <button type="button" class="inline-flex size-9 items-center justify-center rounded-lg text-neutral-600 hover:bg-neutral-100" @click="open = ! open">
                    <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" />
                    </svg>
                </button>
                <ul x-show="open" x-cloak class="absolute left-0 z-30 mt-2 w-56 rounded-xl border border-neutral-200 bg-white p-1 shadow-sm">
                    <x-layouts.app.navigation-links></x-layouts.app.navigation-links>
                </ul>
            </div>
            <a href="/" class="flex justify-center items-center">
                <img src="{{asset(config('app.logo.light') )}}" class="h-6" alt="Logo" />
            </a>
        </div>
        <div class="hidden lg:flex">
            <x-nav>
                <x-layouts.app.navigation-links></x-layouts.app.navigation-links>
            </x-nav>
        </div>
        <div class="flex items-center">
            <x-layouts.app.theme-toggle class="mr-2" />

            @auth
                <x-layouts.app.user-menu></x-layouts.app.user-menu>
            @else
                <x-link class="hidden md:block text-neutral-600 hover:text-neutral-900" href="{{route('login')}}">{{ __('Login') }}</x-link>
                <x-button-link.secondary elementType="a" href="#pricing">{{ __('Get started') }}</x-button-link.secondary>
            @endauth
        </div>
    </div>
</nav>
