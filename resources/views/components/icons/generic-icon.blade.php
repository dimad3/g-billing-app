<button type="{{ $type }}"
    {{ $attributes->merge(['class' => "text-{$color} hover:text-{$hoverColor} inline-flex items-center"]) }}
    title="{{ $title }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"></path>
    </svg>
    {{ $slot }}
</button>

{{-- A generic IconButton component that can be customized with different icons, colors, and titles --}}
{{-- A generic IconButton would be useful if:
You needed many different icon buttons throughout your application
You wanted a consistent way to create various icon buttons with different icons and behaviors
You were building a design system with many reusable components --}}
