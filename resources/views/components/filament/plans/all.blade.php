@props([
    'buyRoute' => 'subscription.change-plan',
])

<section class="bg-white dark:bg-gray-900 p-5 dark:text-white">

        @if(isset($subscription))
            <div class="mx-auto max-w-(--breakpoint-md) text-center mb-8">
                <h2 class="mb-4 text-xl tracking-tight text-gray-900 dark:text-white">{{ __('You are currently on the') }} <span class="inline-flex rounded-full border border-primary-300 bg-primary-50 px-3 py-1 text-base font-semibold text-primary-700">{{ $subscription->plan->product->name }}</span> {{__('plan.')}}</h2>
            </div>
        @endif

        <div class="plan-switcher mx-auto mb-4 flex w-fit justify-center rounded-xl border border-neutral-200 bg-white p-1 dark:border-gray-700 dark:bg-gray-800">
            @foreach($groupedPlans as $interval => $plans)
                <a class="rounded-lg px-3 py-1.5 text-sm font-medium {{$preselectedInterval == $interval ? 'bg-neutral-900 text-white dark:bg-white dark:text-black' : 'text-neutral-600 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-gray-700'}}" data-target="plans-{{$interval}}" aria-selected="{{$preselectedInterval == $interval ? 'true' : 'false'}}">{{str($interval)->title()}}</a>
            @endforeach
        </div>

        @if($isGrouped)
            @foreach($groupedPlans as $interval => $plans)
                <div class="plans-container plans-{{$interval}} {{$preselectedInterval == $interval ? '': 'hidden'}}  grid max-w-md gap-10 row-gap-5 lg:max-w-(--breakpoint-lg) sm:row-gap-10 lg:grid-cols-3 xl:max-w-(--breakpoint-lg) sm:mx-auto dark:text-white pt-5 pb-5">
                    @foreach($plans as $plan)
                        <x-filament.plans.one :plan="$plan" :subscription="$subscription" :buyRoute="$buyRoute" />
                    @endforeach
                </div>
            @endforeach
        @else

            <div class="grid max-w-md gap-10 row-gap-5 lg:max-w-(--breakpoint-lg) sm:row-gap-10 lg:grid-cols-3 xl:max-w-(--breakpoint-lg) sm:mx-auto dark:text-white">
                @foreach($plans as $plan)
                        <x-filament.plans.one :plan="$plan" :subscription="$subscription" :featured="$featured == $plan->product->slug" :buyRoute="$buyRoute"/>
                @endforeach
            </div>
        @endif

</section>

