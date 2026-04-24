<div>
    <form wire:submit="submit">
        {{ $this->form }}


        <div class="container flex mt-4">
            <a class="inline-flex items-center rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-100 mr-4" href="{{ $backUrl }}">
                {{ __('Cancel') }}
            </a>

            <button class="inline-flex items-center rounded-lg bg-primary-700 px-3 py-2 text-sm font-medium text-white hover:bg-primary-800">
                @svg('heroicon-o-plus-circle', 'fi-btn-icon h-5 w-5')
                {{ __('Add discount') }}
            </button>
        </div>

    </form>

{{--    <x-filament-actions::modals />--}}
</div>
