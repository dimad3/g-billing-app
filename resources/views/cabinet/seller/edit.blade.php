<x-app-layout title="{{ __('Update Seller') }}">
    @include('cabinet.seller._nav')
    <x-form.section-container>
        <x-form.section>
            <section>
                <x-form.section-header>
                    <x-slot:title>
                        {{ __('Seller Information') }}
                    </x-slot>

                    {{ $entity->exists ? __('Update seller information.') : __('Fill in seller information.') }}
                </x-form.section-header>

                <form
                    action="{{ $entity->exists ? route('cabinet.seller.update', $entity) : route('cabinet.seller.store') }}"
                    method="POST" class="space-y-6">
                    @if ($entity->exists)
                        @method('PUT')
                    @endif
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Entity Type -->
                        <div>
                            <x-form.input-label for="entity_type" :value="__('Seller Type')" :required="in_array('entity_type', $requiredInputs)" />
                            <x-form.select-input id="entity_type" name="entity_type" :options="$entityTypes" :value="$entity?->entity_type"
                                onchange="toggleNameFields()" />
                            <x-form.input-error :messages="$errors->get('entity_type')" />
                        </div>

                        <!-- Legal Form -->
                        <div>
                            <x-form.input-label for="legal_form" :value="__('Legal Form')" :required="in_array('legal_form', $requiredInputs)" />
                            <x-form.select-input id="legal_form" name="legal_form" :options="config(
                                'static_data.legal_forms.' . old('entity_type', $entity?->entity_type),
                                [],
                            )"
                                :value="$entity?->legal_form" />
                            <x-form.input-error :messages="$errors->get('legal_form')" />
                        </div>

                        <!-- Conditionally rendered fields -->
                        <div id="name_field" class="col-span-2 hidden">
                            <x-form.input-label for="name" :value="__('Name')" :required="in_array('name', $requiredInputs)" />
                            <x-form.text-input id="name" name="name" type="text" :value="$entity?->name" />
                            <x-form.input-error :messages="$errors->get('name')" />
                        </div>

                        <div id="first_name_field" class="hidden">
                            <x-form.input-label for="first_name" :value="__('First Name')" :required="in_array('first_name', $requiredInputs)" />
                            <x-form.text-input id="first_name" name="first_name" type="text" :value="$entity?->first_name" />
                            <x-form.input-error :messages="$errors->get('first_name')" />
                        </div>

                        <div id="last_name_field" class="hidden">
                            <x-form.input-label for="last_name" :value="__('Last Name')" :required="in_array('last_name', $requiredInputs)" />
                            <x-form.text-input id="last_name" name="last_name" type="text" :value="$entity?->last_name" />
                            <x-form.input-error :messages="$errors->get('last_name')" />
                        </div>
                        <!-- End Of Conditionally rendered fields -->

                        <!-- ID Number -->
                        <div>
                            <x-form.input-label for="id_number" :value="__('ID Number')" :required="in_array('id_number', $requiredInputs)" />
                            <x-form.text-input id="id_number" name="id_number" type="text" :value="$entity?->id_number" />
                            <x-form.input-error :messages="$errors->get('id_number')" />
                        </div>

                        <!-- VAT Number -->
                        <div>
                            <x-form.input-label for="vat_number" :value="__('VAT Number')" :required="in_array('vat_number', $requiredInputs)" />
                            <x-form.text-input id="vat_number" name="vat_number" type="text" :value="$entity?->vat_number" />
                            <x-form.input-error :messages="$errors->get('vat_number')" />
                        </div>

                        <!-- Address -->
                        <div class="col-span-2">
                            <x-form.input-label for="address" :value="__('Address')" :required="in_array('address', $requiredInputs)" />
                            <x-form.text-input id="address" name="address" type="text" :value="$entity?->address" />
                            <x-form.input-error :messages="$errors->get('address')" />
                        </div>

                        <!-- City -->
                        <div>
                            <x-form.input-label for="city" :value="__('City')" :required="in_array('city', $requiredInputs)" />
                            <x-form.text-input id="city" name="city" type="text" :value="$entity?->city" />
                            <x-form.input-error :messages="$errors->get('city')" />
                        </div>

                        <!-- Postal Code -->
                        <div>
                            <x-form.input-label for="postal_code" :value="__('Postal Code')" :required="in_array('postal_code', $requiredInputs)" />
                            <x-form.text-input id="postal_code" name="postal_code" type="text" :value="$entity?->postal_code" />
                            <x-form.input-error :messages="$errors->get('postal_code')" />
                        </div>

                        <!-- Note -->
                        <div class="col-span-2">
                            <x-form.input-label for="note" :value="__('Note')" :required="in_array('note', $requiredInputs)" />
                            <x-form.textarea-input id="note" name="note" rows="2" :value="$entity?->note" />
                            <x-form.input-error :messages="$errors->get('note')" />
                        </div>
                    </div>

                    <!-- Bank Accounts Section -->
                    <x-bank-account-manager :banks="$banks ?? collect()" :bank-accounts="$entity->bankAccounts ?? collect()" />

                    <div class="flex items-center gap-4">
                        <x-buttons.secondary-button>
                            {{ __('Save') }}
                        </x-buttons.secondary-button>
                    </div>
                </form>
            </section>
        </x-form.section>
    </x-form.section-container>
</x-app-layout>
