<x-filament-panels::page>

    <script src="https://cdn.paddle.com/paddle/v2/paddle.js"></script>

    @if(config('services.paddle.is_sandbox'))
        <script>
            Paddle.Environment.set("sandbox");
        </script>
    @endif

    <script>
        document.addEventListener("DOMContentLoaded", (event) => {
            Paddle.Setup({
                seller: {{ config('services.paddle.vendor_id') }},
                checkout: {
                    settings: {
                        displayMode: "overlay",
                        theme: "light",
                        successUrl: '{{ $successUrl }}',
                    }
                }
            });
        });
    </script>


    <div class="container">
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm">
            <div>
                <div class="mt-4 flex justify-center">
                    <a class="inline-flex items-center rounded-lg bg-primary-700 px-3 py-2 text-sm font-medium text-white hover:bg-primary-800" href="{{$successUrl}}">
                        {{ __('Back to Subscriptions') }}
                    </a>
                </div>
            </div>
        </div>

    </div>

</x-filament-panels::page>
