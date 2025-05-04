@props([
    'disabled' => false,
    'options' => [], // must be array or simple collection ['id' => 'value']
    'value' => '', // selected value
    'defaultValue' => '',
])

@php
    $oldValue = old($attributes->get('name'), $value);
    $selectedValue = $oldValue ?: $defaultValue; // Use defaultValue if oldValue is empty
@endphp

<select {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge([
        'class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm
                block w-full',
    ]) }}>
    <option value=""></option>
    @foreach ($options as $optionValue => $label)
        <option value="{{ $optionValue }}"
            {{ $optionValue == $selectedValue ? 'selected' : '' }}>
            {{ $label }}
        </option>
    @endforeach
</select>
