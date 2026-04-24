<div>
    <div class="my-4">
        <label class="block w-full max-w-xs space-y-1">
            <span class="block text-sm font-medium text-neutral-700">{{ __('Quantity:') }}</span>
            <div class="relative">
            <input type="number" min="1"
                  {{ $maxQuantity > 0 ? 'max=' . $maxQuantity : '' }}
                   wire:model.blur="quantity"
                   class="h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 pr-9 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none md:w-2/3">

            <div class="absolute right-2 top-2">
                <span wire:loading>
                    <svg class="size-4 animate-spin text-neutral-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M12 2a10 10 0 1 0 10 10" stroke-linecap="round"></path>
                    </svg>
                </span>
            </div>
            </div>
        </label>
        @error('quantity')
            <span class="text-xs text-red-500 mt-1" role="alert">
                {{ $message }}
            </span>
        @enderror
    </div>
</div>
