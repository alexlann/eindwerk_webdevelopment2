<button {{ $attributes->merge(['type' => 'submit', 'class' => 'max-w-xs h-12 w-full text-lg text-center px-4 py-2 border rounded-md tracking-widest hover:bg-pink active:bg-pink focus:outline-none focus:ring ring-pink disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
