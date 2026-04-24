@props(['route' => null, 'href' => null])

@php
    $target = $href;

    if (! $target) {
        if (is_string($route) && str_starts_with($route, '#')) {
            $target = route('home').$route;
        } elseif (is_string($route) && $route !== '') {
            $target = route($route);
        } else {
            $target = '#';
        }
    }

    $selected = is_string($route) && $route !== '' && ! str_starts_with($route, '#') ? request()->routeIs($route) : false;
    $selectedClass = $selected
        ? 'text-neutral-900 bg-neutral-200 md:bg-transparent md:border-b-2 md:border-neutral-900'
        : 'text-neutral-600 hover:bg-neutral-200 hover:text-neutral-900 md:bg-transparent md:border-b-2 md:border-transparent md:hover:border-neutral-300';
@endphp

<li {{ $attributes }}>
    <a href="{{ $target }}" class="block rounded-lg px-3 py-2 text-sm font-medium md:h-full md:rounded-none md:px-4 {{ $selectedClass }}">
        {{ $slot }}
    </a>
</li>
