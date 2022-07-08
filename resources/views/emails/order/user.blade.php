@component('mail::message')
# {{ $visitor->first_name }} {{ $visitor->last_name }} {{__('reserved items from your wishlist')}}

{{ $user->firstname }} {{ $user->lastname }},<br>
<br>
{{ __('The following product(s) were reserved by') }} {{ $visitor->first_name }} {{ $visitor->last_name }}:<br><br>
@foreach ($savedProducts as $savedProduct)
    {{ __("Name") }}: {{$savedProduct->title }}<br>
    {{ __("Price") }}: {{$savedProduct->price }}<br>
    {{ __("Quantity") }}: {{$savedProduct->quantity }}<br><br>
@endforeach
{{ __("With following message") }}: {{$visitor->message }}<br>
<br>
{{ __("Enjoy") }},<br>
{{ config('app.name') }}

@endcomponent
