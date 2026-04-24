<div>
    <div class="mx-4">
        <div class="max-w-3xl rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm mx-auto">
            <div>
                @svg('info', 'w-16 h-16 mx-auto text-primary-500 stroke-primary-500')

                <div class="text-center">
                    <x-heading.h3 class="text-primary-900">
                        {{ __('Verify Your Phone Number To Continue') }}
                    </x-heading.h3>
                    <p>
                        {{ __('Before you can continue, we need to verify your phone number.') }}
                    </p>
                </div>

                @php
                    $validPhoneNumber = !empty($phone) && !$errors->has('phone');
                @endphp

                <div class="mt-8 mx-auto">
                    <div class="flex flex-col gap-4">
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-1" for="phone">
                                {{ __('Your Mobile Phone Number') }}
                            </label>
                            <input type="text" class="h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none" name="phone" required id="phone" wire:model="phone" @disabled($validPhoneNumber)>

                            @error('phone')
                                <span class="text-xs text-red-500" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                            @if ($validPhoneNumber)
                                <a wire:click="editPhone" class="text-primary-500 hover:underline cursor-pointer text-xxs ms-2">
                                    {{ __('Edit Phone Number') }}
                                </a>
                            @endif
                        </div>

                        @if (!empty($phone) && !$errors->has('phone'))
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 mb-1" for="code">
                                    {{ __('Enter Verification Code') }}
                                </label>
                                <input type="text" class="h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none" name="code" required id="code" wire:model="code">

                                @error('code')
                                <span class="text-xs text-red-500" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>

                            <x-button-link.primary class="flex flex-row items-center justify-center gap-3 min-w-64! disabled:opacity-40" elementType="button" wire:click="verifyCode" wire:loading.attr="disabled">
                                {{ __('Verify Phone') }}
                                <div wire:loading class="max-w-fit max-h-fit">
                                    <svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="M12 2a10 10 0 1 0 10 10" stroke-linecap="round"></path>
                                    </svg>
                                </div>
                            </x-button-link.primary>
                        @else
                            <x-button-link.primary class="flex flex-row items-center justify-center gap-3 min-w-64! disabled:opacity-40" elementType="button" wire:click="sendVerificationCode" wire:loading.attr="disabled">
                                {{ __('Send Verification Code') }}
                                <div wire:loading class="max-w-fit max-h-fit">
                                    <svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="M12 2a10 10 0 1 0 10 10" stroke-linecap="round"></path>
                                    </svg>
                                </div>
                            </x-button-link.primary>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
