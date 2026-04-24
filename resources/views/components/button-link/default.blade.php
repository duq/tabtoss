@props(['elementType' => 'a', 'isDisabled' => false])

@php
    $class = 'inline-flex items-center justify-center rounded-lg px-3 py-2 text-sm font-medium transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-500 disabled:cursor-not-allowed disabled:opacity-50';
@endphp

@if($elementType === 'a')
<a
    {{ $attributes->merge(['class' => $class]) }}
    {{ $isDisabled ? 'disabled' : '' }}
>
    {{ $slot }}
</a>
@else
<button
    {{ $attributes->merge(['class' => $class]) }}
    {{ $isDisabled ? 'disabled' : '' }}
>
    {{ $slot }}
</button>
@endif
