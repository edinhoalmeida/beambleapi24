@props(['disabled' => false])
<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->except('select_options')->merge(['class' => 'rounded-md shadow-sm']) !!}>
@foreach ((array) $selectOptions as $code=>$st_code)
	<option value="{{ $code }}">{{ $st_code }}</option>
@endforeach
</select>