<header class="pt-5 h-16 pb-3 fixed top-0 left-0 right-0 bg-white box-content z-50">
    <nav class="container w-10/12 mx-auto">
        <div class="grid grid-cols-7 mb-6 items-center">
            <div class="text-2xl pt-3">
                @if(isset($header["arrowLeft"]))
                    <a href="{{ url()->previous() }}">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                @endif
                @if(isset($header["search"]))
                    <a href="/products">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </a>
                @endif
                @if(isset($header["wishlistVisitor"]))
                    <a href="/wishlist/{{$wishlist->slug}}">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                @endif
                @if(isset($header["arrowCart"]))
                    <a href="/cart">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                @endif
            </div>
                <h1 class="font-serif text-center col-span-5">
                    @hasSection("title")
                        <span class="highlight-container">
                            <span class="highlight">
                                @yield("title")
                            </span>
                        </span>
                    @endif
                </h1>
            <div class="text-right text-2xl pt-3 cursor-pointer" @if(isset($header["menu"])) id="open_menu" @endif>
                @if(isset($header["menu"]))
                    <i class="fa-solid fa-bars"></i>
                @endif
                @if(isset($header["wishlist"]))
                    <a href="/wishlist">
                        <i class="fa-solid fa-baby-carriage"></i>
                    </a>
                @endif
                @if(isset($header["cart"]))
                    <a href="/cart">
                        <i class="fa-solid fa-baby-carriage"></i>
                    </a>
                @endif
                @if(isset($header["logout"]))
                    <form class="flex items-center justify-end" method="POST" action="{{ route("logout") }}">
                        @csrf

                        <a href={{ route("logout") }}
                            class="text-sm pt-6"
                            onclick="event.preventDefault();
                            this.closest('form').submit();">
                            {{ __("Logout") }}
                        </a>
                    </form>
                @endif
            </div>
            @if(isset($header["menu"]))
                <ul id="menu" class="bg-white w-screen-60 sm:w-1/3 lg:w-1/4 xl:w-1/5 h-screen z-30 p-6 absolute top-0 -right-80vw shadow-2xl slide-from-right">
                    <li class="z-40 p-3 text-right text-2xl">
                        <button id="close_menu">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </li>
                    <li class="z-40 py-3">
                        <h1 class="font-serif text-center col-span-5 text-right">
                            <a href="/products">
                                <span class="highlight-container">
                                    <span class="highlight">
                                        {{ __("Products") }}
                                    </span>
                                </span>
                            </a>
                        </h1>
                    </li>
                    <li class="z-40 py-3">
                        <h1 class="font-serif text-center col-span-5 text-right">
                            <a href="/scrape">
                                <span class="highlight-container">
                                    <span class="highlight">
                                        {{ __("Scrape") }}
                                    </span>
                                </span>
                            </a>
                        </h1>
                    </li>
                    <li class="z-40 py-3">
                        <h1 class="font-serif text-center col-span-5 text-right">
                            <a href="/scrape/prices">
                                <span class="highlight-container">
                                    <span class="highlight">
                                        {{ __("Prices") }}
                                    </span>
                                </span>
                            </a>
                        </h1>
                    </li>
                    <li class="p-6">
                        <form method="POST" class="w-full text-right" action="{{ route("logout") }}">
                            @csrf
                            <a href={{ route("logout") }}
                                onclick="event.preventDefault();
                                this.closest('form').submit();">
                                {{ __("Logout") }}
                            </a>
                        </form>
                    </li>
                </ul>
            @endif
        </div>
    </nav>
</header>


