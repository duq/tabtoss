<x-layouts.focus>
    <x-slot name="left">
        <div class="flex flex-col py-2 md:p-10 gap-4 justify-center h-full items-center">
            <div class="w-full rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm md:max-w-xl md:p-8">

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <x-input.field label="{{ __('Email Address') }}" type="email" name="email"
                                   value="{{ $email ?? old('email') }}" required autofocus="true" class="my-2"
                                   autocomplete="email" max-width="w-full"/>

                    @error('email')
                        <span class="text-xs text-red-500" role="alert">
                            {{ $message }}
                        </span>
                    @enderror

                    <x-input.field label="{{ __('Password') }}" type="password" name="password" required class="my-2"  max-width="w-full"/>

                    @error('password')
                        <span class="text-xs text-red-500" role="alert">
                            {{ $message }}
                        </span>
                    @enderror

                    <x-input.field label="{{ __('Confirm Password') }}" type="password" name="password_confirmation" required class="my-2"  max-width="w-full"/>

                    @error('password')
                    <span class="text-xs text-red-500" role="alert">
                            {{ $message }}
                        </span>
                    @enderror

                    <x-button-link.primary class="inline-block w-full! my-2" elementType="button" type="submit">
                        {{ __('Reset Password') }}
                    </x-button-link.primary>

                </form>
            </div>
        </div>
    </x-slot>


    <x-slot name="right">
        <div class="py-4 md:px-12 md:pt-36 h-full">
            <x-heading.h1 class="text-3xl! md:text-4xl! font-semibold!">
                {{ __('Reset Your Password.') }}
            </x-heading.h1>
            <p class="mt-4">
                {{ __('You are 1 step away from resetting your password.') }}
            </p>
        </div>
    </x-slot>

</x-layouts.focus>
