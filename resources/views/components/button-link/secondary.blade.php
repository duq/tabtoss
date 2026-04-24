<x-button-link.default
    {{ $attributes }}
    {{ $attributes->merge(['class' => 'border border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-100'])}}
>
    {{ $slot }}
</x-button-link.default>
