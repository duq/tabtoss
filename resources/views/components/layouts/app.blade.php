<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth bg-white lg:bg-zinc-100 dark:bg-zinc-900 dark:lg:bg-zinc-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.layouts.partials.head')
</head>
<body class="min-h-screen antialiased text-zinc-950 dark:text-zinc-100" x-data>
    <div id="app" class="relative isolate flex min-h-dvh w-full flex-col bg-white lg:bg-zinc-100 dark:bg-zinc-900 dark:lg:bg-zinc-950">
        <x-layouts.app.header />

        <main class="flex flex-1 flex-col pb-2 lg:px-2">
            <div class="grow p-6 lg:rounded-lg lg:bg-white lg:p-10 lg:shadow-xs lg:ring-1 lg:ring-zinc-950/5">
                <div class="mx-auto w-full max-w-6xl">
                    {{ $slot }}
                </div>
            </div>
        </main>

        @include('components.layouts.partials.tail')
    </div>
    <x-impersonate::banner/>
</body>
</html>
