{{-- OTP Login Flow --}}
@if(!$showOtpForm)
    <div class="space-y-1">
        <label class="block text-sm font-medium text-neutral-700" for="email">{{ __('Email Address') }}</label>
        <input type="email" class="h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none" name="email" required id="email" wire:model.live.debounce.500ms="email" value="{{ old('email') }}" />
    </div>

    @error('email')
    <span class="text-xs text-red-500" role="alert">
        {{ $message }}
    </span>
    @enderror

    {{-- Show name field if user doesn't exist --}}
    @if(!$userExists)
        <div class="space-y-1">
            <label class="block text-sm font-medium text-neutral-700" for="name">{{ __('Your Name') }}</label>
            <input type="text" class="h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none" name="name" required id="name" wire:model="name" value="{{ old('name') }}" />
        </div>

        @error('name')
        <span class="text-xs text-red-500" role="alert">
            {{ $message }}
        </span>
        @enderror
    @endif

    @include('livewire.auth.partials.recaptcha')

    {{-- Send OTP Button --}}
    <div class="mt-4">
        <x-button-link.primary
            class="flex flex-row items-center justify-center gap-3 w-full disabled:opacity-40"
            elementType="button"
            type="button"
            wire:click="sendOtpCode"
            wire:loading.attr="disabled">
            {{ $userExists ? __('Send Login Code') : __('Create Account & Send Code') }}
            <div wire:loading wire:target="sendOtpCode" class="max-w-fit max-h-fit">
                <svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M12 2a10 10 0 1 0 10 10" stroke-linecap="round"></path>
                </svg>
            </div>
        </x-button-link.primary>
    </div>

    @if($userExists)
        <div class="my-2 ms-1 text-xs text-neutral-400">{{ __('Click to receive a one-time password via email.') }}</div>
    @else
        <div class="my-2 ms-1 text-xs text-neutral-400">{{ __('Enter your name, then click to create your account and receive a login code.') }}</div>
    @endif

@elseif($showOtpForm)
    <div class="space-y-1">
        <label class="block text-sm font-medium text-neutral-700" for="email">{{ __('Email Address') }}</label>
        <input type="email" class="h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none" name="email" required id="email" wire:model.live.debounce.500ms="email" value="{{ old('email') }}" />
    </div>

    @error('email')
    <span class="text-xs text-red-500" role="alert">
        {{ $message }}
    </span>
    @enderror

    {{-- OTP Input Field --}}
    <div class="space-y-1">
        <label class="block text-sm font-medium text-neutral-700" for="oneTimePassword">{{ __('One-time Password') }}</label>
        <input type="text" class="h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none" name="oneTimePassword" required id="oneTimePassword" wire:model.live="oneTimePassword" />
    </div>

    @error('oneTimePassword')
    <span class="text-xs text-red-500" role="alert">
        {{ $message }}
    </span>
    @enderror

    <div class="my-2 ms-1 text-xs text-neutral-400">{{ __('Enter the one-time password sent to your email address.') }}</div>

    {{-- Resend Code Button --}}
    <div class="mt-2 text-end" x-data="{ resendText: '{{ __('Resend Code') }}', isResending: false }">
        <button
            type="button"
            @click="
                if (!isResending) {
                    isResending = true;
                    resendText = '{{ __('Code sent') }}';
                    $wire.resendOtpCode();
                    setTimeout(() => {
                        resendText = '{{ __('Resend code') }}';
                        isResending = false;
                    }, 2000);
                }
            "
            class="text-primary-500 text-xs cursor-pointer bg-transparent border-0 p-0 m-0 text-left underline"
            :class="{ 'underline': !isResending }"
            x-text="resendText"
        ></button>
    </div>
@endif

