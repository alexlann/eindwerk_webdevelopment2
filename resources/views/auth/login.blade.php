<x-guest-layout>
    @section('title')
        {{ __("Log in") }}
    @endsection
    <x-auth-card>
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" class="max-w-sm" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" class="block mt-1 w-full"
                                    type="email"
                                    name="email"
                                    :value="old('email')"
                                    required autofocus />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />

                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
            </div>

            @if (Route::has('password.request'))
                <p class="text-right">
                    <a class="text-sm text-green hover:text-black" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                </p>
            @endif

            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-3" :errors="$errors" />

            <!-- Remember Me -->
            <div class="block mt-3">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-black text-red focus:border-red focus:ring focus:ring-white focus:ring-opacity-50" name="remember">
                    <span class="ml-2 text-black">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="mt-3">
                <p class="text-sm">{{ __("Don't have an account yet?") }} <a class="text-green hover:text-black" href="/register">{{ __('Register') }}</a></p>

                <x-button class="bg-red text-white border-transparent">
                    {{ __('Log in') }}
                </x-button>

            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
