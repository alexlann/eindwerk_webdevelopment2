@extends('layouts.main')

@extends('partials.header')

@section('content')
    @section('title')
        {{ __("Scrape") }}
    @endsection
    <main class="container mx-auto w-10/12 mt-25 mb-6">
        <div class="mb-3">
            <h2 class="font-bold mb-3 z-20">
                <span class="highlight-container highlight-container-2 z-10">
                    <span class="highlight">
                        {{ __("Scrape webshop") }}
                    </span>
                </span>
            </h2>
            <form action="{{ route('scrape.categories') }}" method="POST">
                @csrf
                <div class="flex justify-between items-center gap-3" >
                    <div class="w-10/12 sm:w-11/12 xl:w-full shrink">
                        <label class="block text-xs text-black -mb-5 ml-2" for="shop">{{ __("Webshop") }}</label>
                        <select class="pt-4 h-12 w-full border-red rounded-md bg-transparent" name="shop" id="shop">
                            @foreach ($shops as $key => $shop)
                                <option value="{{ $key }}">
                                    {{ $shop }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="text-xl h-9 w-9 rounded-full bg-red text-white flex items-center justify-center">
                        +
                    </button>
                </div>
            </form>
        </div>

        <h2 class="font-bold mb-3 z-20">
            <span class="highlight-container highlight-container-2 z-10">
                <span class="highlight">
                    {{ $categories->count() }} {{ __('categories') }}
                </span>
            </span>
        </h2>

        <div class="hide-scroll w-full flex items-center gap-3 overflow-x-auto pb-3">
            @if($filteredShop !== NULL)
                <div class="bg-rose px-3.5 py-1 w-fit shrink-0 flex justify-between items-center gap-2">
                    <div>
                        {{ $filteredShop }}
                    </div>
                    <form class="mb-0" action="{{ route("products.deleteFilter") }}" method="post">
                        @csrf
                        <input type="hidden" name="filterType" value="shop">
                        <button type="submit">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </form>
                </div>
            @endif
            <button class="bg-gray-light px-3.5 py-1 w-fit shrink-0" id="open_shop_scrape">
                <a class="flex justify-between gap-2 items-center" href="#">
                    <div>
                        {{ __("Webshop") }}
                    </div>
                    <div>
                        <i class="fa-solid fa-angle-down"></i>
                    </div>
                </a>
            </button>
        </div>
        <form method="POST" action="{{ route("products.placeFilter") }}" id="menu_shop_scrape" class="bg-white z-30 p-3 absolute top-35 left-9 hidden shadow-2xl mb-0">
            @csrf
            <div class="text-right pb-px">
                <button id="close_shop_scrape">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <ul>
                @foreach ($shops as $key => $shop)
                    <li class="z-40 py-px flex items-center gap-3">
                        <input type="radio" id="{{ $key }}" name="filter" value="{{ $key }}">
                        <label for="{{ $key }}">{{ $shop }}</label>
                    </li>
                @endforeach
            </ul>
            <div class="w-full flex justify-end">
                <input type="hidden" name="filterType" value="shop">
                <button class="border border-red rounded-sm text-red py-px px-3 mt-px" type="submit">{{ __("Filter") }}</button>
            </div>
        </form>

        <div>
            @if($categories->count() > 0)
                @foreach($categories as $category)
                    <form method="POST" action="{{ route("scrape.products") }}" class="mb-0 even:bg-white odd:bg-gray-light p-3 grid grid-cols-7 gap-3">
                        @csrf
                        <div class="col-span-6 gap-3 flex">
                            <p class="text-green">{{ $category->days_between }}</p>
                            <p>{{ $category->title }}</p>
                        </div>
                        <input type="hidden" name="category_id" value="{{ $category->id }}">
                        <button type="submit" class="flex justify-end items-center text-red font-bold">
                            {{ __("Scrape") }}
                        </button>
                    </form>
                @endforeach
            @else
                <div class="flex justify-center">
                    <div class="w-full p-3 border rounded-sm text-red border-red my-3 max-w-xs flex justify-center ">
                        {{ __("No categories found") }}
                    </div>
                </div>
            @endif
        </div>

        @include("partials.status")
    </main>
@endsection
