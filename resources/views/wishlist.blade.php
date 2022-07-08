@extends('layouts.main')

@extends('partials.header')

@section('content')
    @section('title')
        {{ __("Wishlist") }}
    @endsection
    <main class="container mx-auto w-10/12 mt-25 mb-6">
        @if($wishlist)
            <div class="flex justify-center">
                <div class="bg-gray-light w-full rounded p-3 max-w-lg">
                    <div class="grid grid-cols-3 gap-3">
                        <img class="rounded h-full w-full object-cover" src="{{ url('storage/' . $wishlist->image) }}" alt="{{ $wishlist->name }}">
                        <div class="col-span-2">
                            <div class="flex justify-between h-6 mb-1">
                                <h2 class="font-bold mb-6 z-20">
                                    <span class="highlight-container highlight-container-2 z-10">
                                        <span class="highlight">
                                            {{ $wishlist->name }}
                                        </span>
                                    </span>
                                </h2>
                                @auth
                                    <div class="relative">
                                        <button id="wishlist_open">
                                            <i class="fa-solid fa-bars"></i>
                                        </button>
                                        <div id="wishlist_menu" class="bg-white z-30 p-3 absolute top-0 right-0 hidden shadow-2xl text-right">
                                            <button id="wishlist_close" class="p-px">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                            <ul class="w-36">
                                                <li class="z-40 p-px">
                                                    <a href="{{ route("wishlist.edit") }}" class="font-bold z-20">
                                                        <span class="highlight-container highlight-container-2 z-10">
                                                            <span class="highlight">
                                                                {{ __('Edit') }}
                                                            </span>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li class="z-40 p-px">
                                                    <a href="{{ route("login") }}" class="font-bold z-20">
                                                        <span class="highlight-container highlight-container-2 z-10">
                                                            <span class="highlight">
                                                                {{ __('Export') }}
                                                            </span>
                                                        </span>
                                                    </a>
                                                </li>
                                                <li class="z-40 p-px">
                                                    <a href="{{ route("wishlist.close") }}" class="font-bold z-20">
                                                        <span class="highlight-container highlight-container-2 z-10 highlight-container-warning">
                                                            <span class="highlight">
                                                                {{ __('Close wishlist') }}
                                                            </span>
                                                        </span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                @endauth
                            </div>
                            <div>
                                <p>
                                    {{ $wishlist->description }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @auth
                        <div class="bg-white text-green flex justify-between items-center gap-3 p-2 rounded w-full mt-3">
                            <p class="break-all" id="copy_text">
                                @if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){{ "https://" }}@else{{ "http://" }}@endif{{ $_SERVER['HTTP_HOST'] }}/wishlist/{{ $wishlist->slug }}
                            </p>
                            <button id="copy_button">
                                <i class="fa-solid fa-copy"></i>
                            </button>
                        </div>
                    @endauth
                </div>
            </div>
        @else
            @auth
                <x-button class="bg-red text-white">
                    <a href="/wishlist/edit">
                        {{ __('Personalise wishlist') }}
                    </a>
                </x-button>
            @endauth
        @endif

        @if(!$savedProducts->isEmpty())
            <div class="@auth mb-3 @endauth mt-6 grid grid-cols-3 gap-3">
                @auth
                    <div>
                        <p class="font-bold">{{ __("Total") }}:</p>
                        <p class="text-green">{{ __("Reserved") }}:</p>
                    </div>
                    <div>
                        <p class="font-bold">{{ $totalQuantityCount }} @if($totalQuantityCount !== 0){{$totalQuantityCount > 1 ? __('items') : __('item')}} @else {{ __('items') }}@endif</p>
                        <p class="text-green">{{ $orderedQuantityCount }} @if($orderedQuantityCount !== 0){{$orderedQuantityCount > 1 ? __('items') : __('item')}} @else {{ __('items') }}@endif</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold">
                            €{{ number_format((float) $totalPrice, 2, ',', '') }}
                        </p>
                        <p class="text-green">
                            €{{ number_format((float) $orderedPrice, 2, ',', '') }}
                        </p>
                    </div>
                @endauth
                @if($wishlist->isClosed === 1)
                    <div class="font-bold z-20">
                        <span class="highlight-container highlight-container-2 z-10 highlight-container-warning">
                            <span class="highlight">
                                {{ __('Wishlist closed') }}
                            </span>
                        </span>
                    </div>
                @endif
                @guest
                    <h2 class="font-bold mb-3 z-20">
                        <span class="highlight-container highlight-container-2 z-10">
                            <span class="highlight">
                                {{ $notOrderedCount }} @if($notOrderedCount !== 0){{$notOrderedCount > 1 ? __('items') : __('item')}} @else {{ __('items') }}@endif
                            </span>
                        </span>
                    </h2>
                @endguest
            </div>
        @endif
        <div>
            @if(!$savedProducts->isEmpty())
                @foreach ($savedProducts as $savedProduct)
                    {{-- Hide ordered products from visitors --}}
                    @if($savedProduct->visitor_id !== NULL)
                        @auth
                            @include("partials.wishlist-item")
                        @endauth
                    @else
                        @include("partials.wishlist-item")
                    @endif
                @endforeach
            @else
                @auth
                <div class="flex justify-center">
                    <x-button class="bg-red text-white mt-6">
                        <a href="/products">
                            {{ __('Add products') }}
                        </a>
                    </x-button>
                </div>

                @endauth
                @guest
                    <div class="flex justify-center">
                        <div class="w-full p-3 border rounded-sm text-red border-red my-3 max-w-xs flex justify-center ">
                            {{ __("No products found") }}
                        </div>
                    </div>
                @endguest
            @endif
        </div>

        @include("partials.status")
    </main>
@endsection
