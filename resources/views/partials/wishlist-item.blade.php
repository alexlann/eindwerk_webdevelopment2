<div class="mb-0 @if($savedProduct->visitor_id !== NULL) bg-gray-light @endif">
    <a href="/products/{{ $savedProduct->product_id }}" class="border-t border-1 border-gray grid grid-cols-3 gap-3 py-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 2xl:grid-cols-8">
        <img class="rounded w-40 object-cover" src="{{ url('storage/' . $savedProduct->image) }}" alt="{{ $savedProduct->title }}">
        <div class="col-span-2 sm:col-span-3 md:col-span-4 lg:col-span-5 xl:col-span-6 2xl:col-span-7 relative">
            <div class="flex gap-3 items-center">
                <div class="rounded bg-green text-white py-0.5 px-1.5 font-bold pb-0" id="liveScore">{{ $savedProduct->score }}</div>
                <p class="my-1 uppercase text-sm text-green">{{ $savedProduct->store }}</p>
            </div>
            <h3 class="my-1 text-2xl leading-6 font-serif">{{ $savedProduct->title }}</h3>
            <div class="@if($savedProduct->visitor_id === NULL) flex justify-between items-center @endif">
                {{-- Hide delete button if product already ordered or wishlist is closed & show buyer--}}
                <p class="text-green pt-px"><strong class="text-black">€{{ number_format((float) $savedProduct->price, 2, ',', '') }}</strong>@if($savedProduct->quantity > 1) x{{$savedProduct->quantity}}@endif</p>
                @if($savedProduct->visitor_id === NULL && $wishlist->isClosed === NULL)
                    @auth
                        <form method="post" class="mb-0" action="{{ route('savedItem.delete') }}">
                            @method('delete')
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $savedProduct->id }}">
                            <button  class="text-red text-sm" id="delete_button" type="submit">{{ __("Delete") }}</button>
                        </form>
                    @endauth
                @elseif($savedProduct->visitor_id !== NULL)
                    <p class="pt-px font-bold">{{ $savedProduct->orderName }}</p>
                @endif
            </div>
        </div>
    </a>
</div>
