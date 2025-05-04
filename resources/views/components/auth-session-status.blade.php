@props(['status'])

@if ($status)
    <div>
        {{-- <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}> --}}
        <div {{ $attributes->merge(['class' => 'bg-green-100 text-green-700 p-3 rounded mb-4']) }}>
            {{ $status }}
        </div>
    </div>
@endif
