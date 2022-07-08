@extends('layouts.main')

@extends('partials.header')

@section('content')
    @section('title')
        {{ __("Wishlist") }}
    @endsection
    <main class="container mx-auto w-10/12 mt-25 mb-6 max-w-xs">

        <h2 class="font-bold mb-6">
            <span class="highlight-container highlight-container-2">
                <span class="highlight">
                    {{ $wishlist->name }}
                </span>
            </span>
        </h2>

        @if($wishlist->isClosed === 1)
            <div class="w-full p-3 border rounded-sm text-red border-red my-3 max-w-xs flex justify-center ">
                {{ __("Wishlist closed") }}
            </div>
        @else
            <form method="POST" action="{{ route('visitor.storeLogin') }}">
                @csrf

                <!-- Password -->
                <div class="pt-px">
                    <x-label for="password" :value="__('Password')" />

                    <x-input id="password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    autofocus />
                </div>

                <!-- Errors -->
                @if (session("wrongVisitorPassword"))
                    <div class="mt-3 list-inside text-red text-right">
                        <p>{{ __("The password is incorrect.") }}</p>
                    </div>
                @endif

                <div class="@if($errors->any()) mt-3 @else mt-6 @endif">
                    <x-button class="bg-transparent border border-2 border-red text-red">
                        {{ __('Continue') }}
                    </x-button>
                </div>
            </form>
        @endif

        @include("partials.status")
    </main>
@endsection
