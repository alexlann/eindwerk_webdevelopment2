@component('mail::message')
# {{ __('Your order is confirmed') }}

{{ $visitor->first_name }} {{ $visitor->last_name }},<br>
<br>
{{ __('You ordered') }}: <br><br>
@foreach ($savedProducts as $savedProduct)
    {{ __("Name") }}: {{$savedProduct->title }}<br>
    {{ __("Price") }}: {{$savedProduct->price }}<br>
    {{ __("Quantity") }}: {{$savedProduct->quantity }}<br><br>
@endforeach
{{ __("For following wishlist") }}: {{ $wishlist->name }}<br>
{{ __("With following message") }}: {{$visitor->message }}<br>
<br>
{{ __("Thank you for your order") }},<br>
{{ config('app.name') }}

@endcomponent
