<x-guest-layout>
    @section('title')
        {{ __("Register") }}
    @endsection
    <x-auth-card>
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Firstname -->
            <div>
                <x-label for="firstname" :value="__('First name')" />

                <x-input id="firstname" class="block mt-1 w-full" type="text" name="firstname" :value="old('firstname')" required autofocus />
            </div>

            <!-- Lastname -->
            <div class="mt-4">
                <x-label for="lastname" :value="__('Last name')" />

                <x-input id="lastname" class="block mt-1 w-full" type="text" name="lastname" :value="old('lastname')" required />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />

                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-label for="password_confirmation" :value="__('Confirm Password')" />

                <x-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required />
            </div>

            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-3" :errors="$errors" />

            <div class="@if($errors->any()) mt-3 @else mt-6 @endif">
                <p class="text-sm">{{ __("Already have and account?") }} <a class="text-green hover:text-black" href="/login">{{ __('Log in') }}</a></p>

                <x-button class="bg-red text-white border-transparent">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
