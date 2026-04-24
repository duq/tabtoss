<div>
    <div class="max-w-none md:max-w-4xl mx-auto">

        <div class="text-end mx-4 my-6">
            <x-button-link.primary-outline href="{{route('roadmap.suggest')}}">{{ __('+ Suggest a feature') }}</x-button-link.primary-outline>
        </div>

        <div role="tablist" class="mx-auto flex w-fit rounded-xl border border-neutral-200 bg-white p-1 text-center">
            <a href="{{route('roadmap')}}" role="tab" class="rounded-lg px-3 py-1.5 text-sm font-medium {{ request()->get('done', false) ? 'text-neutral-600 hover:bg-neutral-100' : 'bg-neutral-900 text-white' }}" aria-selected="{{ request()->get('done', false) ? 'false' : 'true' }}">{{ __('Active') }}</a>
            <a href="{{route('roadmap', ['done' => true])}}" role="tab" class="rounded-lg px-3 py-1.5 text-sm font-medium {{ request()->get('done', false) ? 'bg-neutral-900 text-white' : 'text-neutral-600 hover:bg-neutral-100' }}" aria-selected="{{ request()->get('done', false) ? 'true' : 'false' }}">{{ __('Done') }}</a>
        </div>


        @if($items->isEmpty())
            <div class="text-center p-4 border border-gray-200 rounded-lg mt-4">
                <p>{{ __('No features found, but you can ') }} <a href="{{route('roadmap.suggest')}}" class="text-primary-500">{{ __('suggest one!') }}</a></p>
            </div>
        @endif

        @foreach($items as $item)
            <x-roadmap.item :item="$item"></x-roadmap.item>
        @endforeach


    </div>

    <div class="mx-auto text-center p-4 md:max-w-lg">
        {{ $items->links() }}
    </div>
</div>
