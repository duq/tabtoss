@props(['backButton' => true])

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.layouts.partials.head')
</head>
<body {{ $attributes->merge(['class' => 'bg-white text-primary-900 dark:bg-slate-950 dark:text-slate-100 w-full']) }} >
    <div id="app">

        <div class="flex justify-between">
            <a href="{{route('home')}}">
                <img src="{{asset(config('app.logo.dark') )}}" class="inline-block h-6 m-6 dark:hidden" alt="Logo" />
                <img src="{{asset(config('app.logo.light') )}}" class="hidden h-6 m-6 dark:inline-block" alt="Logo" />
            </a>

            <div class="self-end m-4 flex items-center gap-3 text-xs text-primary-300">
                <x-layouts.app.theme-toggle class="shrink-0" />

                @if($backButton)
                    <x-link href="{{route('home')}}" class="flex items-center text-primary-200">{{__('<< back')}}</x-link>
                @endif
            </div>
        </div>

        <div>
            {{$slot}}
        </div>

        @include('components.layouts.partials.tail', ['skipCookieContentBar' => true])
    </div>
</body>
</html>
