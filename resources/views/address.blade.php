@extends('layouts.main')

@extends('partials.header')

@section('content')
    @section('title')
        {{ __("Your pram") }}
    @endsection
    <main class="container mx-auto w-10/12 mt-25 mb-6">

        <h2 class="font-bold mb-6">
            <span class="highlight-container highlight-container-2">
                <span class="highlight">
                    {{ __('Personal information') }}
                </span>
            </span>
        </h2>

        <form method="POST" action="{{ route('visitor.storeAddress') }}">
            @csrf
            <div class="sm:grid sm:gap-3 sm:grid-cols-2 lg:grid-cols-3 ">
                <!-- Firstname -->
                <div>
                    <x-label for="firstname" :value="__('First name')" />

                    <input id="firstname" class="block mt-1 w-full pt-6 h-12 rounded-md focus:border-red focus:ring focus:ring-pink focus:ring-opacity-50"
                                    type="text"
                                    name="firstname"
                                    value="@if(old('firstname')){{ old('firstname ') }}@elseif($visitor !== NULL && $visitor->first_name !== NULL){{ $visitor->first_name }}@endif"
                                    autofocus />
                </div>

                <!-- Lastname -->
                <div class="mt-4 sm:mt-0">
                    <x-label for="lastname" :value="__('Last name')" />

                    <input id="lastname" class="block mt-1 w-full pt-6 h-12 rounded-md focus:border-red focus:ring focus:ring-pink focus:ring-opacity-50"
                                    type="text"
                                    name="lastname"
                                    value="@if(old('lastname')){{ old('lastname ') }}@elseif($visitor !== NULL && $visitor->last_name !== NULL){{ $visitor->last_name }}@endif"
                                    autofocus />
                </div>

                <!-- Email -->
                <div class="mt-4 lg:mt-0">
                    <x-label for="email" :value="__('Email')" />

                    <input id="email" class="block mt-1 w-full pt-6 h-12 rounded-md focus:border-red focus:ring focus:ring-pink focus:ring-opacity-50"
                                    type="email"
                                    name="email"
                                    value="@if(old('email')){{ old('email ') }}@elseif($visitor !== NULL && $visitor->email !== NULL){{ $visitor->email }}@endif"
                                    autofocus />
                </div>

                <!-- Street -->
                <div class="mt-4">
                    <x-label for="street" :value="__('Street')" />

                    <input id="street" class="block mt-1 w-full pt-6 h-12 rounded-md focus:border-red focus:ring focus:ring-pink focus:ring-opacity-50"
                                    type="text"
                                    name="street"
                                    value="@if(old('street')){{ old('street ') }}@elseif($visitor !== NULL && $visitor->street !== NULL){{ $visitor->street }}@endif"
                                    autofocus />
                </div>

                <!-- City -->
                <div class="mt-4">
                    <x-label for="city" :value="__('City')" />

                    <input id="city" class="block mt-1 w-full pt-6 h-12 rounded-md focus:border-red focus:ring focus:ring-pink focus:ring-opacity-50"
                                    type="text"
                                    name="city"
                                    value="@if(old('city')){{ old('city ') }}@elseif($visitor !== NULL && $visitor->city !== NULL){{ $visitor->city }}@endif"
                                    autofocus />
                </div>

                <!-- Zipcode -->
                <div class="mt-4">
                    <x-label for="zipcode" :value="__('Zip / Postal Code')" />

                    <input id="zipcode" class="block mt-1 w-full pt-6 h-12 rounded-md focus:border-red focus:ring focus:ring-pink focus:ring-opacity-50"
                                    type="text"
                                    name="zipcode"
                                    value="@if(old('zipcode')){{ old('zipcode ') }}@elseif($visitor !== NULL && $visitor->zipcode !== NULL){{ $visitor->zipcode }}@endif"
                                    autofocus />
                </div>

            </div>

            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-3" :errors="$errors" />

            <div class="flex justify-center @if($errors->any()) mt-3 @else mt-6 @endif">
                <x-button class="bg-transparent border border-2 border-red text-red">
                    {{ __('Continue') }}
                </x-button>
            </div>
        </form>

        @include("partials.status")
    </main>
@endsection
