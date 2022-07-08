@extends("layouts.main")

@extends("partials.header")

@section("content")
    @section("title")
        {{ __("Wishlist") }}
    @endsection
    <main class="container mx-auto w-10/12 mt-25 mb-6">

        <h2 class="font-bold mb-6">
            <span class="highlight-container highlight-container-2">
                <span class="highlight">
                    {{ __("Edit wishlist") }}
                </span>
            </span>
        </h2>

        <form enctype="multipart/form-data" method="POST" action="{{ route("wishlist.store") }}">
            @csrf

            <!-- Wishlist name -->
            <div>
                <x-label for="name" :value="__('Wishlist name')" />

                <input id="name" class="block mt-1 w-full pt-6 h-12 rounded-md focus:border-red focus:ring focus:ring-pink focus:ring-opacity-50"
                                type="text"
                                name="name"
                                value="@if(old('name')){{ old('name') }}@elseif($wishlist !== NULL && $wishlist->name !== NULL){{ $wishlist->name }}@endif"
                                autofocus />
            </div>

            <!-- Description -->
            <div class="mt-4">
                <x-label for="description" :value="__('Message')" />

                <textarea id="description" class="block mt-1 w-full pt-6 rounded-md focus:border-red focus:ring focus:ring-pink focus:ring-opacity-50"
                                type="text"
                                name="description"
                                rows="5"
                                >@if(old('description')){{ old('description') }}@elseif($wishlist !== NULL && $wishlist->description !== NULL){{ $wishlist->description }}@endif</textarea>
            </div>

            <!-- Slug -->
            <div class="mt-4">
                <x-label for="slug" :value="__('https://wishlist.alexlann.com/wishlist/')" />

                <input id="slug" class="block mt-1 w-full pt-6 h-12 rounded-md focus:border-red focus:ring focus:ring-pink focus:ring-opacity-50"
                                type="text"
                                name="slug"
                                value="@if(old('slug')){{ old('slug') }}@elseif($wishlist !== NULL && $wishlist->slug !== NULL){{ $wishlist->slug }}@endif"
                                autofocus />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="slug" :value="__('Password')" />

                <input id="password" class="block mt-1 w-full pt-6 h-12 rounded-md focus:border-red focus:ring focus:ring-pink focus:ring-opacity-50"
                                type="password"
                                name="password"
                                value
                                autofocus />
            </div>

            <!-- Image -->
            <div class="mt-4">
                <label for="image" class="block text-xs text-black">
                   {{ __('Image') }}
                </label>

                <input type="file" name="image" id="image">
            </div>

            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-3" :errors="$errors" />

            <div class="flex justify-center @if($errors->any()) mt-3 @else mt-6 @endif">
                <x-button class="bg-transparent border border-2 border-red text-red">
                    {{ __('Edit') }}
                </x-button>
            </div>
        </form>

        @include("partials.status")
    </main>
@endsection
