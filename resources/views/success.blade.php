@extends('layouts.main')

@extends('partials.header')

@section('content')
    @section('title')
        {{ __("Success") }}
    @endsection
    <main class="container mx-auto w-10/12 mt-25 mb-6">
        <div class="flex justify-center text-center">
            <div class="w-full p-3 border rounded-sm text-green border-green my-3 max-w-xs flex justify-center ">
                {{ __("The payment was successful.") }}<br>
                {{ __("Thank you for your order") }}.
            </div>
        </div>
    </main>

@endsection
