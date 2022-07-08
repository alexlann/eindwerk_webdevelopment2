@extends("layouts.main")

@extends("partials.header")

@section("content")
    <main class="container mx-auto w-10/12 mt-25 mb-6">

        <div class="lg:flex lg:justify-between lg:gap-6">
            <div>
                <div class="flex justify-center">
                    <img class="rounded max-w-sm h-full w-full object-cover" src="{{  url('storage/' . $productImages[0]->name) }}" id="main_image" alt="{{ $product->title }}">
                </div>
                @if($productImages->count() > 1)
                    <div class="flex justify-center gap-1 mt-3">
                        @foreach ($productImages as $key => $productImage)
                            <input type="hidden" name="imageTitle" id="img_title_{{$key}}" value="{{$productImage->name}}">
                            <button class="border border-green rounded-full h-3 w-3 @if($productImage === $productImages[0]) bg-green @endif" id="img_button_{{$key}}"></button>
                        @endforeach
                        <input type="hidden" name="img_count" id="img_count" value="{{ $productImages->count() }}">
                    </div>
                @endif
            </div>
            <div class="w-full">
                <div>
                    <p class="my-3 uppercase text-green">{{ $shops[$product->store] }}</p>
                    <h1 class="font-serif leading-8 my-3">
                        {{ $product->title }}
                    </h1>
                    <p class="font-bold my-3 text-2xl">€{{ number_format((float) $product->price, 2, ",", "") }}</p>
                    <div class="leading-5 my-3">
                        @if($product->description)
                            <p>{{ $product->description }}
                        </p>@endif
                    </div>
                </div>
                {{-- if is not admin, or if is visitor--}}
                @if((auth()->user() !== NULL && !auth()->user()->isAdmin) || (auth()->user() === NULL))
                    <form method="POST" action="@if(auth()->user()){{ route("product.store", $product->id) }}@else{{ route("cart.store") }}@endif" class="w-full my-6 bg-gray-light p-3">
                        @csrf
                        <div class="flex items-center gap-3">
                            <label for="score">{{ __("Score") }}</label>
                            @auth
                                {{-- if product already ordered, don't show inputfield --}}
                                @if($product->visitor_id === NULL && $wishlist->isClosed === NULL)
                                    <input type="range" min="0" max="10" value=@if(old("score")){{old("score")}}@elseif($product->score !== NULL){{$product->score}}@else{{8}}@endif class="slider" id="score" name="score" oninput="this.nextElementSibling.value = this.value">
                                    <output class="rounded bg-green text-white py-0.5 px-1.5 font-bold pb-0" id="liveScore">@if(old("score")){{old("score")}}@elseif($product->score !== NULL){{$product->score}}@else{{8}}@endif</output>
                                @endif
                            @endauth
                            {{-- if product already ordered, don't show inputfield --}}
                            @if(auth()->user() !== NULL && $product->visitor_id !== NULL || auth()->user() === NULL)
                                <output class="rounded bg-green text-white py-0.5 px-1.5 font-bold pb-0" id="liveScore">{{ $product->score }}</output>
                            @endif
                            </div>
                        <div class="flex items-center justify-between mt-3">
                            <div class="flex gap-3">
                                {{-- if product already ordered, or wishlist closed, don't show inputfield --}}
                                @if(auth()->user() !== NULL && $product->visitor_id !== NULL || auth()->user() !== NULL && $wishlist->isClosed === 1 || auth()->user() === NULL)
                                    {{ __("Quantity") }}
                                    <p class="font-bold" id="quantity_text">{{$product->quantity}}</p>
                                @endif
                                @auth
                                    {{-- if product already ordered, don't show inputfields --}}
                                    @if($product->visitor_id === NULL && $wishlist->isClosed === NULL)
                                    {{-- Make border-pink and text-pink accessible for js--}}
                                        <input type="hidden" class="border-pink text-pink">
                                        <button disabled id="quantity_decrease" class="rounded-full text-lg border border-1 border-gray w-6 h-6 flex items-center justify-center text-gray">-</button>
                                        <p id="quantity_text">@if(old("quantity")){{old("quantity")}}@elseif($product->quantity !== NULL){{$product->quantity}}@else{{1}}@endif</p>
                                        <input value="@if(old("quantity")){{old("quantity")}}@elseif($product->quantity !== NULL){{$product->quantity}}@else{{1}}@endif" type="hidden" name="quantity" id="quantity">
                                        <button id="quantity_increase" class="rounded-full text-lg border border-1 border-green w-6 h-6 flex items-center justify-center text-green">+</button>
                                    @endif
                                @endauth
                            </div>
                            {{-- if product already ordered, or wishlist closed don't show button --}}
                            @if(auth()->user() !== NULL && $product->visitor_id === NULL && $wishlist->isClosed === NULL || auth()->user() === NULL)
                                <div class="flex items-center gap-3 w-fit">
                                    <p class="w-fit z-20">
                                        <span class="highlight-container highlight-container-2 z-10">
                                            <span class="highlight">
                                                {{-- Check if product is being edited by user --}}
                                                @if($product->quantity !== NULL && auth()->user()){{ __("Update pram") }}@else{{ __("Add to pram") }}@endif
                                            </span>
                                        </span>
                                    </p>
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <button type="submit" class="h-12 w-12 rounded-full bg-red text-white flex items-center justify-center">
                                        @include("partials.icons.pram")
                                    </button>
                                </div>
                            @endif
                        </div>
                    </form>
                @endif

                <div class="my-3 break-all">
                    <p>
                        {{ __("Original link") }}: <strong class="text-green font-normal">{{$product->detail_url }}</strong>
                    </p>
                </div>
            </div>
        </div>

        @auth
            <div class="flex gap-3 flex-wrap border-t border-gray border-1 pt-3 mt-6 mb-6">
                @foreach ($categories as $category)
                    <div class="bg-rose w-fit py-1 px-3">
                        <a href="/products/filter/{{ $category->category_id }}">
                            {{ $category->title }}
                        </a>
                    </div>
                @endforeach
            </div>
        @endauth

        @include("partials.status")
    </main>

@endsection
