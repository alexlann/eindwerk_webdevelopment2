<div class="overflow-auto mb-3">
    <div class="hide-scroll w-full flex gap-3 overflow-x-auto items-center">
        @if($sort !== NULL)
            <div class="bg-rose px-3.5 py-1 w-fit shrink-0 flex justify-between items-center gap-2">
                <div>
                    {{ $sort }}
                </div>
                <form class="mb-0" action="{{ route("products.deleteFilter") }}" method="post">
                    @csrf
                    <input type="hidden" name="filterType" value="sort">
                    <button type="submit">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </form>
            </div>
        @endif
        @if($filter !== NULL)
            <div class="bg-rose px-3.5 py-1 w-fit shrink-0 flex justify-between items-center gap-2">
                <div>
                    {{ $filter }}
                </div>
                <form class="mb-0" action="{{ route("products.deleteFilter") }}" method="post">
                    @csrf
                    <input type="hidden" name="filterType" value="filter">
                    <button type="submit">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </form>
            </div>
        @endif
        @if($filteredCategory !== NULL)
            <div class="bg-rose px-3.5 py-1 w-fit shrink-0 flex justify-between items-center gap-2">
                <div>
                    {{ $filteredCategory }}
                </div>
                <form class="mb-0" action="{{ route("products.deleteFilter") }}" method="post">
                    @csrf
                    <input type="hidden" name="filterType" value="categoryId">
                    <button type="submit">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </form>
            </div>
        @endif
        <button class="bg-gray-light px-3.5 py-1 w-fit shrink-0" id="open_sort">
            <a class="flex justify-between gap-2 items-center" href="#">
                <div>
                    {{ __("Sort") }}
                </div>
                <div>
                    <i class="fa-solid fa-angle-down"></i>
                </div>
            </a>
        </button>
        <button class="bg-gray-light px-3.5 py-1 w-fit shrink-0" id="open_filter">
            <a class="flex justify-between gap-2 items-center" href="#">
                <div>
                    {{ __("Filter") }}
                </div>
                <div>
                    <i class="fa-solid fa-angle-down"></i>
                </div>
            </a>
        </button>
        <button class="bg-gray-light px-3.5 py-1 w-fit shrink-0" id="open_categories">
            <a class="flex justify-between gap-2 items-center" href="#">
                <div>
                    {{ __("Category") }}
                </div>
                <div>
                    <i class="fa-solid fa-angle-down"></i>
                </div>
            </a>
        </button>
    </div>
    <div id="menu_sort" class="bg-white z-30 p-3 absolute top-35 left-9 hidden shadow-2xl">
        <div class="flex p-3 justify-between">
            <h2 class="font-bold z-20">
                <span class="highlight-container highlight-container-2 z-10">
                    <span class="highlight">
                        {{ __('Price') }}
                    </span>
                </span>
            </h2>
            <button id="close_sort" class="text-right">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form class="mb-0" action="{{ route("products.placeFilter") }}" method="POST">
            @csrf
            <ul>
                <li class="z-40 py-px flex items-center gap-3">
                    <input type="radio" id="price_ascending" name="filter" value="ASC">
                    <label for="price_ascending">Ascending</label>
                </li>
                <li class="z-40 py-px flex items-center gap-3">
                    <input type="radio" id="price_descending" name="filter" value="DESC">
                    <label for="price_descending">Descending</label>
                </li>
            </ul>
            <div class="w-full flex justify-end">
                <input type="hidden" name="filterType" value="sort">
                <button class="border border-red rounded-sm text-red py-px px-3 mt-px" type="submit">{{ __("Filter") }}</button>
            </div>
        </form>
    </div>
    <div id="menu_filter" class="bg-white z-30 p-3 absolute top-35 left-24 hidden shadow-2xl w-36">
        <div class="flex p-3 justify-between">
            <h2 class="font-bold z-20">
                <span class="highlight-container highlight-container-2 z-10">
                    <span class="highlight">
                        {{ __('Price') }}
                    </span>
                </span>
            </h2>
            <button id="close_filter" class="text-right">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form class="mb-0" action="{{ route("products.placeFilter") }}" method="POST">
            @csrf
            <ul>
                <li class="z-40 py-px flex items-center gap-3">
                    <input type="radio" id="price_xs" name="filter" value="0-10">
                    <label for="price_xs">€0-10</label>
                </li>
                <li class="z-40 py-px flex items-center gap-3">
                    <input type="radio" id="price_sm" name="filter" value="10-25">
                    <label for="price_sm">€10-25</label>
                </li>
                <li class="z-40 py-px flex items-center gap-3">
                    <input type="radio" id="price_md" name="filter" value="25-50">
                    <label for="price_md">€25-50</label>
                </li>
                <li class="z-40 py-px flex items-center gap-3">
                    <input type="radio" id="price_lg" name="filter" value="50-100">
                    <label for="price_lg">€50-100</label>
                </li>
                <li class="z-40 py-px flex items-center gap-3">
                    <input type="radio" id="price_xl" name="filter" value="100-5000">
                    <label for="price_xl">€100-5000</label>
                </li>
            </ul>
            <div class="w-full flex justify-end">
                <input type="hidden" name="filterType" value="filter">
                <button class="border border-red rounded-sm text-red py-px px-3 mt-px" type="submit">{{ __("Filter") }}</button>
            </div>
        </form>
    </div>
    <form method="POST" action="{{ route("products.placeFilter") }}" id="menu_categories" class="bg-white z-30 p-3 absolute top-35 right-9 hidden shadow-2xl mb-0">
        @csrf
        <div class="text-right p-3 pb-0">
            <button id="close_categories">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <ul>
            @foreach ($categories as $category)
                @if($category->scraped_on !== NULL)
                    <li class="z-40 py-px flex items-center gap-3">
                        <input type="radio" id="category_{{ $category->id }}" name="filter" value="{{ $category->id }}">
                        <label for="category_{{ $category->id }}">{{ $category->title }}</label>
                    </li>
                @endif
            @endforeach
        </ul>
        <div class="w-full flex justify-end">
            <input type="hidden" name="filterType" value="categoryId">
            <button class="border border-red rounded-sm text-red py-px px-3 mt-px" type="submit">{{ __("Filter") }}</button>
        </div>
    </form>
</div>
