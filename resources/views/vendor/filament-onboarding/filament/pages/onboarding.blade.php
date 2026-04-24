<x-filament-panels::page>
    <div class="py-4 sm:py-6">
        <div class="mx-auto w-full max-w-5xl space-y-6 sm:space-y-8">
            <div class="relative space-y-4 text-center">
                @if (\Saasykit\FilamentOnboarding\FilamentOnboardingPlugin::get()->isSkippable())
                    <a
                        wire:click="skip"
                        class="absolute right-0 top-0 inline-flex items-center rounded-xl px-3 py-2 text-sm font-medium text-neutral-500 hover:bg-neutral-100 hover:text-neutral-900"
                    >
                        {{ __('Skip Onboarding') }}
                    </a>
                @endif

                <span class="inline-flex items-center rounded-full border border-neutral-200 bg-neutral-50 px-3 py-1 text-sm font-medium text-neutral-700">
                    {{ __('3-step setup') }}
                </span>
                <h1 class="text-3xl font-semibold tracking-tight text-balance text-neutral-900 sm:text-4xl">
                    {{ __('Welcome to TabToss!') }}
                </h1>
                <p class="mx-auto max-w-[72ch] text-base text-pretty text-neutral-500">
                    {{ __('Let\'s get you started by importing your bookmarks and creating new categories for them.') }}
                </p>
            </div>

            <div class="rounded-3xl border border-neutral-200 bg-white p-4 sm:p-6">
                <form wire:submit="submit" class="space-y-6">
                    {{ $this->form }}

                    <div class="flex items-center justify-center border-t border-neutral-200 pt-5">
                        <x-filament::button type="submit" size="lg">
                            {{ __('Complete onboarding') }}
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-filament-panels::page>
