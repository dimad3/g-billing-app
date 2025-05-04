<x-app-layout title="{{ $document->exists ? __('Update Invoice') : __('Create New Invoice') }}">
    @include('cabinet.documents._nav')

    <div>
        <form
            action="{{ $document->exists ? route('cabinet.documents.update', $document) : route('cabinet.documents.store') }}"
            method="POST" class="space-y-6">

            @if ($document->exists)
                @method('PUT')
            @endif
            @csrf

            <x-invoice.section-info :document="$document" :clients="$clients" :documentTypes="$documentTypes" :statuses="$statuses"
                :defaultDocumentDate="$defaultDocumentDate" :defaultNumber="$defaultNumber" :defaultStatus="$defaultStatus" :requiredInputs="$requiredInputs" />

            <x-invoice.items-table :document="$document" />

            <x-invoice.additional-info :document="$document" :agents="$agents" :defaultAgentId="$defaultAgentId" :requiredInputs="$requiredInputs" />
        </form>
    </div>

    <script>
        // Make defaultTaxRate available to document-form.js
        window.defaultTaxRate = @json($defaultTaxRate);
    </script>
</x-app-layout>
