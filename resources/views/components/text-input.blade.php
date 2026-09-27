@props(['disabled' => false])

@php
    $field = $attributes->get('name');
    $invalid = filled($field) && $errors->has($field);
@endphp

<input @disabled($disabled) {{ $attributes->merge(['class' => ($invalid ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-slate-300 focus:border-brand focus:ring-brand').' rounded-md shadow-sm']) }}>
