@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font text-sm text-green']) }}>
        {{ $status }}
    </div>
@endif
