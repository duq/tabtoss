<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-white dark:bg-zinc-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.layouts.partials.head')
</head>
<body class="bg-white text-zinc-900 dark:bg-zinc-900 dark:text-zinc-100">
    <div id="app">

        <div class="w-full">
            <div class="flex flex-col-reverse flex-wrap md:flex-nowrap md:flex-row">
                 <div class="md:basis-3/5 flex flex-col">
                     <div class="hidden md:block">
                         <a href="{{route('home')}}">
                            <img src="{{asset(config('app.logo.dark') )}}" class="inline-block h-6 m-6 dark:hidden" alt="Logo" />
                            <img src="{{asset(config('app.logo.light') )}}" class="hidden h-6 m-6 dark:inline-block" alt="Logo" />
                         </a>
                     </div>

                     {{$left}}
                 </div>
                <div class="md:basis-2/5 md:min-h-screen md:bg-linear-to-r md:from-primary-400 md:to-primary-700 flex flex-col text-zinc-900 text-center md:text-left md:text-white left-shadow dark:border-l dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-100">
                    <div class="flex justify-between md:justify-end">
                        <div class="md:hidden">
                            <a href="{{route('home')}}">
                                <img src="{{asset(config('app.logo.dark') )}}" class="inline-block h-6 m-6 dark:hidden" alt="Logo" />
                                <img src="{{asset(config('app.logo.light') )}}" class="hidden h-6 m-6 dark:inline-block" alt="Logo" />
                            </a>
                        </div>

                        <div class="self-end m-4 flex items-center gap-3 text-xs text-zinc-500 md:text-primary-200 dark:text-zinc-400">
                            <x-layouts.app.theme-toggle class="shrink-0" />
                            <x-link href="{{route('home')}}" class="flex items-center text-zinc-700 md:text-primary-100 dark:text-zinc-300">{{__('< back home')}}</x-link>
                        </div>
                    </div>

                    {{$right}}
                </div>
            </div>
        </div>

        @include('components.layouts.partials.tail')
    </div>
</body>
</html>
