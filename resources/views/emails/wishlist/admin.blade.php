@component('mail::message')
# {{ $user->firstname }} {{ $user->lastname }} {{ __('wishes to close their wishlist') }}

{{ $user->firstname }} {{ $user->lastname }} {{ __("requested to close the following wishlist") }}: {{ $wishlist->name }}<br>

{{ config('app.name') }}

@endcomponent
