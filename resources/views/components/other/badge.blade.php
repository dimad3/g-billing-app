@props(['dataSet' => 'default', 'key' => 'default',])

@php
    // Define color sets for different badge types
    $colorSets = [
        'statuses' => [
            'draft' => 'bg-gray-50 text-gray-600 ring-gray-500/10',
            'sent' => 'bg-blue-50 text-blue-700 ring-blue-700/10',
            'disputed' => 'bg-purple-50 text-purple-700 ring-purple-700/10',
            'cancelled' => 'bg-yellow-50 text-yellow-800 ring-yellow-600/20',
            'paid' => 'bg-green-50 text-green-700 ring-green-600/20',
            'overdue' => 'bg-red-50 text-red-700 ring-red-600/10',
        ],
        'roles' => [
            'admin' => 'bg-red-100 text-red-800 ring-red-600/10',
            'user' => 'bg-blue-100 text-blue-800 ring-blue-600/10',
        ],
        'default' => [
            'default' => 'bg-gray-50 text-gray-600 ring-gray-500/10', // Fallback
        ],
    ];

    // Determine which color set to use
    $selectedColors = $colorSets[$dataSet] ?? $colorSets['default'];

    // Get the badge color based on the status or use default
    $badgeColor = $selectedColors[$key] ?? $colorSets['default']['default'];

    // Common badge styles
    $commonClasses = 'inline-flex items-center rounded-md px-1 py-0.5 text-xs font-medium ring-1 ring-inset';

    // Final combined classes
    $badgeClasses = trim("$commonClasses $badgeColor");
@endphp

<span class="{{ $badgeClasses }}">
    {{ $slot }}
</span>

{{-- Example of how to use the badge component in a Blade view:
<div class="flex space-x-2">
    <x-other.badge type="statuses" key="draft">Doc is Draft</x-other.badge>
    <x-other.badge type="statuses" key="sent">Doc Sent</x-other.badge>
    <x-other.badge type="roles" key="admin">Administrator</x-other.badge>
    <x-other.badge type="roles" key="user">Simple User</x-other.badge>
</div> --}}
