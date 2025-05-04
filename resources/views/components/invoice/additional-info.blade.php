<section class="bg-white p-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Due Date -->
        <div>
            <x-form.input-label for="due_date" :value="__('Due Date')" :required="in_array('due_date', $requiredInputs)" />
            <x-form.text-input id="due_date" name="due_date" type="text" :value="$document->due_date" />
            <x-form.input-error :messages="$errors->get('due_date')" />
        </div>

        <!-- Delivery Date -->
        <div>
            <x-form.input-label for="delivery_date" :value="__('Delivery Date')" />
            <x-form.text-input id="delivery_date" name="delivery_date" type="text" :value="$document->delivery_date" />
            <x-form.input-error :messages="$errors->get('delivery_date')" />
        </div>

        <!-- Transaction Description -->
        <div>
            <x-form.input-label for="transaction_description" :value="__('Transaction Description')" />
            <x-form.text-input id="transaction_description" name="transaction_description" type="text"
                :value="$document->transaction_description" />
            <x-form.input-error :messages="$errors->get('transaction_description')" />
        </div>

        <!-- Tax Note -->
        <div>
            <x-form.input-label for="tax_note" :value="__('Tax Note')" />
            <x-form.text-input id="tax_note" name="tax_note" type="text" :value="$document->tax_note" />
            <x-form.input-error :messages="$errors->get('tax_note')" />
        </div>

        <!-- Document Note -->
        <div class="col-span-2">
            <x-form.input-label for="document_note" :value="__('Document Note')" />
            <x-form.textarea-input id="document_note" name="document_note" rows="2" :value="$document->document_note" />
            <x-form.input-error :messages="$errors->get('document_note')" />
        </div>

        <!-- Agent -->
        <div class="col-span-2">
            <x-form.input-label for="agent_id" :value="__('Agent')" />
            <x-form.select-input id="agent_id" name="agent_id" :options="$agents" :value="$document->agent_id"
                :defaultValue="$defaultAgentId" />
            <x-form.input-error :messages="$errors->get('agent_id')" />
        </div>

        <div class="col-span-2 flex justify-center gap-8">
            <!-- Show Created By -->
            <div>
                <div class="inline-flex items-center">
                    <x-form.checkbox-input id="show_created_by" name="show_created_by" :checked="$document->show_created_by" />
                    <x-form.input-label for="show_created_by" :value="__('Show Created By')" />
                </div>
                <x-form.input-error :messages="$errors->get('show_created_by')" />
            </div>

            <!-- Show Signature -->
            <div>
                <div class="inline-flex items-center">
                    <x-form.checkbox-input id="show_signature" name="show_signature" :checked="$document->show_signature" />
                    <x-form.input-label for="show_signature" :value="__('Show Signature')" />
                </div>
                <x-form.input-error :messages="$errors->get('show_signature')" />
            </div>
        </div>

        <x-invoice.form-actions :document="$document" />
    </div>
</section>
