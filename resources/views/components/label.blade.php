@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs text-black -mb-6 ml-2']) }}>
    {{ $value ?? $slot }}
</label>
