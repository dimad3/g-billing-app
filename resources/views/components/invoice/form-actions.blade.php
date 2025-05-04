<div class="col-span-2 flex justify-center gap-4 mb-4">
    <x-buttons.secondary-button>
        {{ $document->exists ? __('Update') : __('Create') }}
    </x-buttons.secondary-button>

    @if ($document->exists)
        <x-links.primary-link href="{{ route('cabinet.documents.generate-invoice', [$document]) }}" target="_blank">
            {{ __('Download PDF') }}
        </x-links.primary-link>
    @endif

    <x-links.light-link href="{{ route('cabinet.documents.index') }}">
        {{ __('Cancel') }}
    </x-links.light-link>
</div>
