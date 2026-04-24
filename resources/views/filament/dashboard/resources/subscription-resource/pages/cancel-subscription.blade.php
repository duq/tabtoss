<x-filament-panels::page xmlns:x-filament-panels="http://www.w3.org/1999/html">

    <div class="container">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-8">
            <h2 class="text-xl font-semibold text-neutral-900">{{ __('We are sad to see you leaving :(') }}</h2>
            <p class="mt-3">{{ __('Is there anything we can do to change your mind?') }}</p>
            <div class="mt-4 flex justify-center">
                <a class="inline-flex items-center rounded-lg bg-primary-700 px-3 py-2 text-sm font-medium text-white hover:bg-primary-800" href="mailto:{{@config('app.support_email')}}">{{ svg('heroicon-m-chat-bubble-left-ellipsis', 'fi-btn-icon h-5 w-5') }}
                    {{ __('Contact Us') }}</a>
            </div>
            <div class="flex justify-center">
                <a class="text-sm mt-3 mx-5 underline" href="{{$confirmCancelUrl}}">{{ __('No, proceed with cancellation') }}</a>
            </div>
        </div>

    </div>

</x-filament-panels::page>
