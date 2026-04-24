<x-button-link.default
    {{ $attributes }}
    {{ $attributes->merge(['class' => 'border border-primary-300 bg-white text-primary-700 hover:bg-primary-50']) }}
>
    {{ $slot }}
</x-button-link.default>
