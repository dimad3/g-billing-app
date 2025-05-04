<section class="bg-white p-4 mt-4">
    <x-form.section-header>
        <x-slot:title>
            {{ $document->exists ? __('Invoice Information') : __('Create New Invoice') }}
        </x-slot>
    </x-form.section-header>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Client -->
        <div>
            <x-form.input-label for="client_id" :value="__('Client')" :required="in_array('client_id', $requiredInputs)" />
            <x-form.select-input id="client_id" name="client_id" :options="$clients" :value="$document->client_id"
                onchange="processClientDefaults()" />
            <x-form.input-error :messages="$errors->get('client_id')" />
        </div>

        <!-- Document Date -->
        <div>
            <x-form.input-label for="document_date" :value="__('Issued')" :required="in_array('document_date', $requiredInputs)" />
            <x-form.text-input id="document_date" name="document_date" type="text" :value="$document->document_date"
                :defaultValue="$defaultDocumentDate" />
            <x-form.input-error :messages="$errors->get('document_date')" />
        </div>

        <!-- Document Number -->
        <div>
            <x-form.input-label for="number" :value="__('Invoice Number')" :required="in_array('number', $requiredInputs)" />
            <x-form.text-input id="number" name="number" type="text" :value="$document->number"
                :defaultValue="$defaultNumber" />
            <x-form.input-error :messages="$errors->get('number')" />
        </div>

        <!-- Document Type -->
        <div>
            <x-form.input-label for="document_type" :value="__('Document Type')" :required="in_array('document_type', $requiredInputs)" />
            <x-form.select-input id="document_type" name="document_type" :options="$documentTypes"
                :value="$document->document_type" />
            <x-form.input-error :messages="$errors->get('document_type')" />
        </div>

        <!-- Status -->
        <div>
            <x-form.input-label for="status" :value="__('Status')" :required="in_array('status', $requiredInputs)" />
            <x-form.select-input id="status" name="status" :options="$statuses" :value="$document->status"
                :defaultValue="$defaultStatus" />
            <x-form.input-error :messages="$errors->get('status')" />
        </div>
    </div>
</section>
