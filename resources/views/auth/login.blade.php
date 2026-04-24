<x-layouts.focus>
    <x-slot name="left">
        <div class="flex flex-col py-2 md:p-10 gap-4 justify-center h-full items-center">
            <div class="w-full rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm md:max-w-xl md:p-8">

                @if($isOtpLoginEnabled)
                    <livewire:auth.login.one-time-password-login />
                @else
                    @include('auth.partials.traditional-login-form')
                @endif

            </div>
        </div>
    </x-slot>


    <x-slot name="right">
        <div class="py-4 md:px-12 md:pt-36 h-full">
            <x-heading.h1 class="text-3xl! md:text-4xl! font-semibold!">
                {{ __('Login.') }}
            </x-heading.h1>
            <p class="mt-4">
                {{ __('It\'s great to see you back again :)') }}
            </p>
        </div>
    </x-slot>

</x-layouts.focus>
