<form method="POST" action="{{ route('login') }}">
    @csrf

    <p class="text-xs mt-2 text-end">{{__('No account?')}} <a class="text-primary-500 font-bold" href="{{ route('register') }}">{{__('Register')}}</a></p>

    <x-input.field label="{{ __('Email Address') }}" type="email" name="email"
                   value="{{ old('email') }}" required autofocus="true"
                   autocomplete="email" max-width="w-full"/>
    @error('email')
        <span class="text-xs text-red-500" role="alert">
            {{ $message }}
        </span>
    @enderror

    <x-input.field label="{{ __('Password') }}" type="password" name="password" required  max-width="w-full"/>

    @error('password')
        <span class="text-xs text-red-500" role="alert">
            {{ $message }}
        </span>
    @enderror

    @if (config('app.recaptcha_enabled'))
        <div class="my-4">
            {!! htmlFormSnippet() !!}
        </div>

        @error('g-recaptcha-response')
        <span class="text-xs text-red-500" role="alert">
                {{ $message }}
            </span>
        @enderror

        @push('tail')
            {!! htmlScriptTagJsApi() !!}
        @endpush

    @endif

    <div class="mt-4 mb-4 flex flex-wrap gap-2 justify-between text-sm">
        <div class="flex gap-2">
            <input class="mt-0.5 size-4 rounded border-neutral-300 text-primary-700 focus:ring-primary-500"
                   type="checkbox" name="remember"
                   id="remember" {{ old('remember') ? 'checked' : '' }}>

            <label class="text-sm" for="remember">
                {{ __('Remember Me') }}
            </label>
        </div>
        <div>
            @if (Route::has('password.request'))
                <a class="text-primary-500 text-xs" href="{{ route('password.request') }}">
                    {{ __('Forgot Your Password?') }}
                </a>
            @endif
        </div>
    </div>

    <x-button-link.primary class="inline-block w-full! my-2" elementType="button" type="submit">
        {{ __('Login') }}
    </x-button-link.primary>


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

</form>
