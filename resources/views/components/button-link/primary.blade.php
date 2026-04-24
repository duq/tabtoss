<x-button-link.default
    {{ $attributes }}
    {{ $attributes->merge(['class' => 'bg-primary-700 text-white hover:bg-primary-800']) }}
>
    {{ $slot }}
</x-button-link.default>
