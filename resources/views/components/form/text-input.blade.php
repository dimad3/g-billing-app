@props([
    'disabled' => false,
    'value' => '',
    'autocomplete' => 'off',
    'defaultValue' => '',
])

@php
    $name = $attributes->get('name');
    $oldValue = old($name);

    if (is_array($oldValue)) {
        $displayValue = ''; // Prevent displaying array as a string
    } elseif (!is_null($oldValue)) {
        $displayValue = $oldValue; // Use old value if it exists, even if it's 0 or false
    } elseif (!empty($value) || $value == 0) {
        $displayValue = $value; // Use passed-in value (from controller/data binding)
    } else {
        $displayValue = $defaultValue; // Fallback to defaultValue
    }
@endphp

<input @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full',
        'value' => $displayValue,
        'autocomplete' => $autocomplete,
    ]) }}>
