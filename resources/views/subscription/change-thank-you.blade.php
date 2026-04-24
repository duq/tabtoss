<x-layouts.focus-center>

    <div class="mx-4">
        <div class="mx-auto max-w-3xl rounded-2xl border border-neutral-200 bg-white p-6 text-center shadow-sm">
            <div>
                @svg('party', 'w-24 h-24 mx-auto text-primary-500 stroke-primary-500')
                <x-heading.h3 class="text-primary-900">
                    {{ __('Thank you for your purchase!') }}
                </x-heading.h3>
                <p>
                    {{ __('It might take a few moments for your subscription to be updated.') }}
                </p>

                <x-button-link.primary href="{{ route('home') }}" class="mt-4 mx-auto">
                    {{ __('Continue Your Journey') }}
                </x-button-link.primary>

            </div>
        </div>
    </div>

</x-layouts.focus-center>
