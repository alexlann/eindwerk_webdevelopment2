@extends('layouts.main')

@extends('partials.header')

@section('content')
    @section('title')
        {{ __("Prices") }}
    @endsection
        <main class="container mx-auto w-10/12 mt-25 mb-6">
            <h2 class="font-bold mb-3 z-20">
                <span class="highlight-container highlight-container-2 z-10">
                    <span class="highlight">
                        {{ __("Update prices") }}
                    </span>
                </span>
            </h2>

            <div>
                @if($categories->count() > 0)
                    @foreach($categories as $category)
                        @if($category->days_between !== NULL)
                            <form method="POST" action="{{ route("scrape.prices") }}" class="mb-0 even:bg-white odd:bg-gray-light p-3 grid grid-cols-7 gap-3">
                                @csrf
                                <div class="col-span-6 flex gap-3">
                                    <p class="text-green">{{ $category->days_between }}</p>
                                    <p>{{ $category->title }}</p>
                                </div>
                                <input type="hidden" name="category_id" value="{{ $category->id }}">
                                <button type="submit" class="flex justify-end items-center">
                                    <i class="fa-solid fa-arrows-rotate pr-3"></i>
                                </button>
                            </form>
                        @endif
                    @endforeach
                @else
                    <div class="flex justify-center ">
                        <div class="w-full p-3 border rounded-sm text-red border-red my-3 max-w-xs flex justify-center ">
                            {{ __("No categories found") }}
                        </div>
                    </div>
                @endif
            </div>

            @include("partials.status")
        </main>
@endsection
