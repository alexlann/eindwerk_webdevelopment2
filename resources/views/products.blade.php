@extends('layouts.main')

@extends('partials.header')

@section('content')
    @section('title')
        {{ __("Products") }}
    @endsection
    <main class="container mx-auto w-10/12 mt-25 mb-6">

        @include('partials.filter')

        @if($products->count() > 0)
            <div class="grid grid-cols-2 gap-3 mb-6 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                @foreach ($products as $product)
                    <div>
                        <a href="/products/{{ $product->id }}">
                            <img class="rounded object-contain w-full max-h-40" src="{{ url('storage/' . $product->image) }}" alt="{{ $product->title }}">
                            <div>
                                <p class="my-1 uppercase text-xs text-green">{{ $shops[$product->store] }}</p>
                                <p class="my-1 leading-5">{{ $product->title }}</p>
                                <p class="font-bold">€{{ number_format((float) $product->price, 2, ',', '') }}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex justify-center ">
                <div class="w-full p-3 border rounded-sm text-red border-red flex justify-center ">
                    {{ __("No products found") }}
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                {{ $products->links() }}
            </div>
        </div>

        @include("partials.status")
    </main>

@endsection
