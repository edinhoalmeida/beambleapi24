@props(['messages'])
@if(!empty($messages))
<span {{ $attributes->merge(['class' => 'error invalid-feedback']) }}>
@foreach ((array) $messages as $message)
    {{ $message }}<br>
@endforeach
</span>
@endif
