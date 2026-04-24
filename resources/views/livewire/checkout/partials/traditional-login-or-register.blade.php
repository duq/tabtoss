<div class="space-y-1">
    <label class="block text-sm font-medium text-neutral-700" for="email">{{ __('Email Address') }}</label>
    <input type="email" class="h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none" name="email" required id="email" wire:model.blur="email" value="{{ old('email') }}" />
</div>

@error('email')
<span class="text-xs text-red-500" role="alert">
    {{ $message }}
</span>
@enderror


@if(!empty($email))
    <div class="space-y-1">
        <label class="block text-sm font-medium text-neutral-700" for="password">{{ __('Password') }}</label>
        <input type="password" class="h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none" name="password" required id="password" wire:model="password" />
    </div>

    @error('password')
    <span class="text-xs text-red-500 ms-1" role="alert">
        {{ $message }}
    </span>
    @enderror
@endif

@if ($userExists)
    <div class="my-2 ms-1 text-xs text-neutral-400">{{ __('You are already registered, enter your password.') }}</div>
@elseif(!empty($email))
    <div class="my-2 ms-1 text-xs text-neutral-400">{{ __('Enter a password for your new account.') }}</div>
@endif

@if($userExists)
    @if (Route::has('password.request'))
        <div class="text-end">
            <a class="text-primary-500 text-xs" href="{{ route('password.request') }}">
                {{ __('Forgot Your Password?') }}
            </a>
        </div>
    @endif
@endif


@if(!$userExists || empty($email))

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
