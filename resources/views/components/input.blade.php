@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'max-w-xs pt-6 h-12 rounded-md focus:border-red focus:ring focus:ring-pink focus:ring-opacity-50']) !!}>
