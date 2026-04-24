@guest()
    <div class="mb-4">

        <x-heading.h2 class="text-primary-900 text-xl!">
            {{ __('Enter your details') }}
        </x-heading.h2>

        <div class="relative rounded-2xl border border-neutral-200 mt-4 overflow-hidden p-6">

            @if (!empty($intro))
                <div class="mb-4 text-sm">
                    {{ $intro }}
                </div>
            @endif

            <div class="absolute top-0 right-0 p-2">
                    <span wire:loading>
                        <svg class="size-4 animate-spin text-neutral-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M12 2a10 10 0 1 0 10 10" stroke-linecap="round"></path>
                        </svg>
                    </span>
            </div>

            @if($otpEnabled)
                @include('livewire.checkout.partials.one-time-password')
            @else
                @include('livewire.checkout.partials.traditional-login-or-register')
            @endif

            @if(empty($email))
                <x-auth.social-login>
                    <x-slot name="before">
                        <div class="flex flex-col w-full">
                            <div class="my-2 flex items-center gap-3 text-xs uppercase tracking-wide text-neutral-400">
                                <span class="h-px flex-1 bg-neutral-200"></span>
                                <span>{{ __('or') }}</span>
                                <span class="h-px flex-1 bg-neutral-200"></span>
                            </div>
                        </div>
                    </x-slot>
                </x-auth.social-login>
            @endif

        </div>
    </div>

@endguest
