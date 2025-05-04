@props(['id', 'name', 'value' => 1, 'checked' => false, 'disabled' => false])

@php
    $isChecked = old($name, $checked) == $value;
@endphp

<label for="{{ $id }}" class="flex items-center cursor-pointer relative">
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}"
        {{ $isChecked ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }}
        class="rounded border-gray-300 text-indigo-600 shadow-sm
        focus:ring-indigo-500 focus:ring-2 focus:ring-offset-2">
</label>
<label for="{{ $id }}" class="cursor-pointer ml-2 text-slate-600 text-sm">
    {{ $slot }}
</label>
