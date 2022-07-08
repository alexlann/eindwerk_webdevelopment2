@component('mail::message')
# {{ __('Your wishlist will be closed') }}

{{ $user->firstname }} {{ $user->lastname }},

{{ __("You requested to close the following wishlist") }}: {{ $wishlist->name }}
{{ __("Your money will be deposited in the following days") }}

{{ __("Thanks,") }}<br>
{{ config('app.name') }}

@endcomponent
