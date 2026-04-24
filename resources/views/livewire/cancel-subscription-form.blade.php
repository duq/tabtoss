<div>

    <form wire:submit="cancel">
        {{ $this->form }}

        <p class="text-sm mt-3 italic">
            {{ __('Once you cancel, your subscription will be active until the end of your billing period. You will be able to continue using the service until the end of your billing period.') }}
        </p>

        <div class="container flex mt-4">
            <a class="inline-flex items-center rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-100 mr-4" href="{{ $backUrl }}">
                {{ __('Cancel') }}
            </a>

            <button class="inline-flex items-center rounded-lg bg-primary-700 px-3 py-2 text-sm font-medium text-white hover:bg-primary-800">
                @svg('heroicon-s-power', 'fi-btn-icon h-5 w-5')
                {{ __('Confirm Subscription Cancellation') }}
            </button>
        </div>

    </form>

    <x-filament-actions::modals />
</div>
