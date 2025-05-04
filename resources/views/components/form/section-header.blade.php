<header>
    <h2 class="text-lg font-medium text-gray-900">
        {{ $title }}
    </h2>

    <p class="mt-1 text-sm text-gray-600">
        {{ $slot }}
    </p>
</header>

{{-- This Blade template defines a header of section with a title and a paragraph.
The {{ $title }} and {{ $slot }} are Blade directives that output the values
of the $title and $slot variables, respectively --}}

{{-- component can render multiple different slots in different locations within the component.
The injection of an aditional "title" slot is allowed --}}
