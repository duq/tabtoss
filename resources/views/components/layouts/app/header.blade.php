@php
    $stackedNavItems = [
        ['label' => __('Home'), 'url' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => __('Bookmarks'), 'url' => route('bookmarks.index'), 'active' => request()->routeIs('bookmarks.index')],
        ['label' => __('Inbox'), 'url' => route('bookmarks.inbox'), 'active' => request()->routeIs('bookmarks.inbox')],
        ['label' => __('Dashboard'), 'url' => url('/dashboard'), 'active' => request()->is('dashboard*')],
    ];
@endphp

<div x-data="{ mobileNavOpen: false, teamMenuOpen: false, profileMenuOpen: false }">
    <livewire:announcement.view />

    <div x-show="mobileNavOpen" x-cloak class="fixed inset-0 z-40 bg-black/30 lg:hidden" @click="mobileNavOpen = false"></div>
    <div x-show="mobileNavOpen" x-cloak class="fixed inset-y-0 left-0 z-50 w-full max-w-80 p-2 lg:hidden">
        <div class="flex h-full flex-col rounded-lg bg-white shadow-xs ring-1 ring-zinc-950/5 dark:bg-zinc-900 dark:ring-white/10">
            <div class="-mb-3 px-4 pt-3">
                <button type="button" class="inline-flex items-center gap-3 rounded-lg p-2 text-left text-base/6 font-medium text-zinc-950 hover:bg-zinc-950/5 sm:text-sm/5 dark:text-zinc-100 dark:hover:bg-white/5" @click="mobileNavOpen = false" aria-label="{{ __('Close navigation') }}">
                    <svg viewBox="0 0 20 20" class="size-5 fill-zinc-500 dark:fill-zinc-400" aria-hidden="true">
                        <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"></path>
                    </svg>
                </button>
            </div>
            <div class="px-3 pb-4 pt-4">
                <div class="mb-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ config('app.name') }}</div>
                <ul role="list" class="space-y-1">
                    @foreach ($stackedNavItems as $item)
                        <li>
                            <a href="{{ $item['url'] }}" @class([
                                'flex items-center rounded-lg px-3 py-2 text-sm/5 font-medium',
                                'bg-zinc-950/5 text-zinc-950 dark:bg-white/10 dark:text-zinc-100' => $item['active'],
                                'text-zinc-700 hover:bg-zinc-950/5 hover:text-zinc-950 dark:text-zinc-300 dark:hover:bg-white/5 dark:hover:text-zinc-100' => ! $item['active'],
                            ])>
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <header class="flex h-14 items-center border-b border-zinc-950/10 bg-white px-4 lg:bg-zinc-100 dark:border-white/10 dark:bg-zinc-900 dark:lg:bg-zinc-950">
        <div class="py-2.5 lg:hidden">
            <button type="button" class="inline-flex items-center gap-3 rounded-lg p-2 text-left text-base/6 font-medium text-zinc-950 hover:bg-zinc-950/5 sm:text-sm/5 dark:text-zinc-100 dark:hover:bg-white/5" @click="mobileNavOpen = true" aria-label="{{ __('Open navigation') }}">
                <svg viewBox="0 0 20 20" class="size-5 fill-zinc-500 dark:fill-zinc-400" aria-hidden="true">
                    <path d="M2 6.75C2 6.33579 2.33579 6 2.75 6H17.25C17.6642 6 18 6.33579 18 6.75C18 7.16421 17.6642 7.5 17.25 7.5H2.75C2.33579 7.5 2 7.16421 2 6.75ZM2 13.25C2 12.8358 2.33579 12.5 2.75 12.5H17.25C17.6642 12.5 18 12.8358 18 13.25C18 13.6642 17.6642 14 17.25 14H2.75C2.33579 14 2 13.6642 2 13.25Z"></path>
                </svg>
            </button>
        </div>

        <div class="min-w-0 flex-1">
            <nav class="flex h-full flex-1 items-center gap-4">
                <div class="relative max-lg:hidden" @click.outside="teamMenuOpen = false">
                    <button type="button" class="relative flex min-w-0 items-center gap-3 rounded-lg p-2 text-left text-base/6 font-medium text-zinc-950 hover:bg-zinc-950/5 sm:text-sm/5 dark:text-zinc-100 dark:hover:bg-white/5" @click="teamMenuOpen = ! teamMenuOpen">
                        <span class="inline-flex size-7 items-center justify-center rounded-md border border-zinc-300 bg-white text-xs font-semibold text-zinc-700 dark:border-white/10 dark:bg-zinc-800 dark:text-zinc-200">{{ strtoupper(substr(config('app.name'), 0, 1)) }}</span>
                        <span class="truncate">{{ config('app.name') }}</span>
                        <svg viewBox="0 0 20 20" class="size-4 fill-zinc-500 dark:fill-zinc-400" aria-hidden="true">
                            <path d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.167l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z"></path>
                        </svg>
                    </button>
                    <div x-show="teamMenuOpen" x-cloak class="absolute left-0 z-30 mt-2 min-w-64 rounded-xl border border-zinc-950/10 bg-white p-1 shadow-sm dark:border-white/10 dark:bg-zinc-900">
                        <a href="{{ url('/dashboard') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-white/5">{{ __('Settings') }}</a>
                    </div>
                </div>

                <div class="h-6 w-px bg-zinc-950/10 max-lg:hidden dark:bg-white/10"></div>

                <div class="flex h-full items-center gap-3 max-lg:hidden">
                    @foreach ($stackedNavItems as $item)
                        <span class="relative flex h-full items-center">
                            @if ($item['active'])
                                <span class="absolute inset-x-2 -bottom-2.5 h-0.5 rounded-full bg-zinc-950 dark:bg-white"></span>
                            @endif
                            <a href="{{ $item['url'] }}" class="relative flex min-w-0 items-center gap-3 rounded-lg p-2 text-left text-base/6 font-medium text-zinc-950 hover:bg-zinc-950/5 sm:text-sm/5 dark:text-zinc-100 dark:hover:bg-white/5">
                                <span>{{ $item['label'] }}</span>
                            </a>
                        </span>
                    @endforeach
                </div>

                <div class="-ml-4 flex-1" aria-hidden="true"></div>

                <div class="flex items-center gap-3">
                    <x-layouts.app.theme-toggle class="shrink-0" />

                    <a href="#" class="relative flex min-w-0 items-center gap-3 rounded-lg p-2 text-left text-base/6 font-medium text-zinc-950 hover:bg-zinc-950/5 sm:text-sm/5 dark:text-zinc-100 dark:hover:bg-white/5" aria-label="{{ __('Search') }}">
                        @svg('heroicon-m-magnifying-glass', 'size-5 text-zinc-500 dark:text-zinc-400')
                    </a>
                    <a href="{{ route('bookmarks.inbox') }}" class="relative flex min-w-0 items-center gap-3 rounded-lg p-2 text-left text-base/6 font-medium text-zinc-950 hover:bg-zinc-950/5 sm:text-sm/5 dark:text-zinc-100 dark:hover:bg-white/5" aria-label="{{ __('Inbox') }}">
                        @svg('heroicon-m-inbox', 'size-5 text-zinc-500 dark:text-zinc-400')
                    </a>

                    <div class="relative" @click.outside="profileMenuOpen = false">
                        <button type="button" class="relative flex min-w-0 items-center gap-3 rounded-lg p-2 text-left text-base/6 font-medium text-zinc-950 hover:bg-zinc-950/5 sm:text-sm/5 dark:text-zinc-100 dark:hover:bg-white/5" @click="profileMenuOpen = ! profileMenuOpen">
                            <span class="inline-flex size-7 items-center justify-center rounded-md bg-zinc-900 text-xs font-semibold text-white">
                                {{ strtoupper(substr(optional(auth()->user())->name ?? 'U', 0, 1)) }}
                            </span>
                        </button>

                        <div x-show="profileMenuOpen" x-cloak class="absolute right-0 z-30 mt-2 min-w-64 rounded-xl border border-zinc-950/10 bg-white p-1 shadow-sm dark:border-white/10 dark:bg-zinc-900">
                            @auth
                                <a href="{{ url('/dashboard/my-profile') }}" class="flex rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-white/5">{{ __('My profile') }}</a>
                                <a href="{{ url('/dashboard') }}" class="flex rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-white/5">{{ __('Settings') }}</a>
                                <div class="my-1 border-t border-zinc-950/10 dark:border-white/10"></div>
                                <a href="{{ route('privacy-policy') }}" class="flex rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-white/5">{{ __('Privacy policy') }}</a>
                                <div class="my-1 border-t border-zinc-950/10 dark:border-white/10"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full rounded-lg px-3 py-2 text-left text-sm text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-white/5">{{ __('Sign out') }}</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="flex rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-white/5">{{ __('Login') }}</a>
                                <a href="{{ route('register') }}" class="flex rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-white/5">{{ __('Register') }}</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </header>
</div>
