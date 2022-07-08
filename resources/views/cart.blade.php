@extends('layouts.main')

@extends('partials.header')

@section('content')
    @section('title')
        {{ __("Your pram") }}
    @endsection
    <main class="container mx-auto w-10/12 mt-25  mb-6">
        <div>
            <h2 class="font-bold mb-3 z-20">
                <span class="highlight-container highlight-container-2 z-10">
                    <span class="highlight">
                        @if($cart !== NULL & $cart->getContent()->count() > 0){{$cart->getContent()->count()}} {{$cart !== NULL & $cart->getContent()->count() > 1 ? __('items') : __('item')}} @else 0 {{ __('items') }}@endif
                    </span>
                </span>
            </h2>
            @if($cart !== NULL && $cart->getContent()->count() > 0)
                @foreach ($cart->getContent() as $product)
                    <div class="mb-6">
                        <div class="bg-gray-light grid grid-cols-3 gap-3 p-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 2xl:grid-cols-8">
                            <img class="rounded w-40 object-cover" src="{{  url('storage/' . $product->attributes["image"]) }}" alt="{{ $product->name }}">
                            <div class="col-span-2 sm:col-span-3 md:col-span-4 lg:col-span-5 xl:col-span-6 2xl:col-span-7">
                                <div class="flex gap-3 items-center">
                                    <div class="rounded bg-green text-white py-0.5 px-1.5 font-bold pb-0" id="liveScore">{{ $product->attributes["score"] }}</div>
                                    <p class="my-1 uppercase text-sm text-green">{{ $product->attributes["store"] }}</p>
                                </div>
                                <h3 class="font-serif my-1 text-2xl leading-6">{{ $product->name }}</h3>
                                <div class="flex justify-between items-center">
                                    <p class="text-green"><strong class="text-black">€{{ number_format((float) $product->price, 2, ',', '') }}</strong>@if($product->quantity > 1) x{{$product->quantity}}@endif</p>
                                    <form method="post" class="mb-0" action="{{ route('cart.delete') }}">
                                        @method('delete')
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <button class="text-red text-sm" id="delete_button" type="submit">{{ __("Delete") }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="w-full p-3 border rounded-sm text-red border-red my-3 max-w-xs flex justify-center ">
                    {{ __("You don't have any items in your pram yet") }}
                </div>
            @endif
            <div class="flex justify-between">
                <h2 class="font-bold mb-3 z-20">
                    {{ __('Total') }}
                </h2>
                <p><strong>€{{ number_format((float) $cart->getTotal(), 2, ",", "") }}</strong></p>
            </div>
        </div>

        <div class="border-t border-1 border-gray pt-3 mb-3">
            <h2 class="font-bold mb-3 z-20">
                <span class="highlight-container highlight-container-2 z-10">
                    <span class="highlight">
                        {{ __('Personal information') }}
                    </span>
                </span>
            </h2>
            <div class="bg-gray-light w-full rounded p-3 relative">
                <p><strong>{{ $visitor !== NULL ? $visitor->first_name . " " . $visitor->last_name : __("Name") }}</strong></p>
                <p>{{ $visitor !== NULL ? $visitor->email : __("Email") }}</p>
                <p>{{ $visitor !== NULL ? $visitor->street : __("Street") }}</p>
                <p>{{ $visitor !== NULL ? $visitor->zipcode : __("Zip / Postal Code") }} {{ $visitor !== NULL ? $visitor->city : __("City") }}</p>
                <a href="/address" class="text-green absolute top-3 right-3"><i class="fa-solid fa-pen"></i></a>
            </div>
        </div>

        <form action="{{ route("checkout") }}" method="post">
            @csrf
            <label for="message" class="border-t border-1 border-gray pt-3 mb-3">
                <h2 class="font-bold mb-3 z-20">
                    <span class="highlight-container highlight-container-2 z-10">
                        <span class="highlight">
                            {{ __('Personal message') }}
                        </span>
                    </span>
                </h2>
                <textarea value="@if(old('message')){{ old('message') }}@elseif($visitor !== NULL && $visitor->message !== NULL){{ $visitor->message }}@endif" name="message" id="message" class="bg-gray-light w-full rounded p-3 border-none" placeholder="{{ __("A personal message to the parents...") }}"></textarea>
            </label>

            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-3" :errors="$errors" />

            <div class="flex justify-center @if($errors->any()) mt-3 @else mt-6 @endif">
                <x-button class="bg-red text-white">
                    {{ __('Pay') }}
                </x-button>
            </div>
        </form>

        @include("partials.status")
    </main>

@endsection
