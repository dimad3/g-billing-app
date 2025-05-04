<x-app-layout title="{{ $client->exists ? __('Update Client') : __('Add New Client') }}">
    @include('cabinet.clients._nav')
    <x-form.section-container>
        <x-form.section>
            <section>
                <x-form.section-header>
                    <x-slot:title>
                        {{ $client->exists ? __('Client Information') : __('Add New Client') }}
                    </x-slot>

                    {{ $client->exists ? __('Update client information.') : __('Fill in client information.') }}
                </x-form.section-header>

                <form
                    action="{{ $client->exists ? route('cabinet.clients.update', $client) : route('cabinet.clients.store') }}"
                    method="POST" class="space-y-6">
                    @if ($client->exists)
                        @method('PUT')
                    @endif
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Entity Type -->
                        <div>
                            <x-form.input-label for="entity_type" :value="__('Client Type')" :required="in_array('entity_type', $requiredInputs)" />
                            <x-form.select-input id="entity_type" name="entity_type" :options="$entityTypes" :value="$client->entity?->entity_type"
                                onchange="toggleNameFields()" />
                            <x-form.input-error :messages="$errors->get('entity_type')" />
                        </div>

                        <!-- Legal Form -->
                        <div>
                            <x-form.input-label for="legal_form" :value="__('Legal Form')" :required="in_array('legal_form', $requiredInputs)" />
                            <x-form.select-input id="legal_form" name="legal_form" :options="config(
                                'static_data.legal_forms.' . old('entity_type', $client->entity?->entity_type),
                                [],
                            )"
                                :value="$client->entity?->legal_form" />
                            <x-form.input-error :messages="$errors->get('legal_form')" />
                        </div>

                        <!-- Conditionally rendered fields -->
                        <div id="name_field" class="col-span-2 hidden">
                            <x-form.input-label for="name" :value="__('Name')" :required="in_array('name', $requiredInputs)" />
                            <x-form.text-input id="name" name="name" type="text" :value="$client->entity?->name" />
                            <x-form.input-error :messages="$errors->get('name')" />
                        </div>

                        <div id="first_name_field" class="hidden">
                            <x-form.input-label for="first_name" :value="__('First Name')" :required="in_array('first_name', $requiredInputs)" />
                            <x-form.text-input id="first_name" name="first_name" type="text" :value="$client->entity?->first_name" />
                            <x-form.input-error :messages="$errors->get('first_name')" />
                        </div>

                        <div id="last_name_field" class="hidden">
                            <x-form.input-label for="last_name" :value="__('Last Name')" :required="in_array('last_name', $requiredInputs)" />
                            <x-form.text-input id="last_name" name="last_name" type="text" :value="$client->entity?->last_name" />
                            <x-form.input-error :messages="$errors->get('last_name')" />
                        </div>
                        <!-- End Of Conditionally rendered fields -->

                        <!-- ID Number -->
                        <div>
                            <x-form.input-label for="id_number" :value="__('ID Number')" :required="in_array('id_number', $requiredInputs)" />
                            <x-form.text-input id="id_number" name="id_number" type="text" :value="$client->entity?->id_number" />
                            <x-form.input-error :messages="$errors->get('id_number')" />
                        </div>

                        <!-- VAT Number -->
                        <div>
                            <x-form.input-label for="vat_number" :value="__('VAT Number')" :required="in_array('vat_number', $requiredInputs)" />
                            <x-form.text-input id="vat_number" name="vat_number" type="text" :value="$client->entity?->vat_number" />
                            <x-form.input-error :messages="$errors->get('vat_number')" />
                        </div>

                        <!-- Address -->
                        <div class="col-span-2">
                            <x-form.input-label for="address" :value="__('Address')" :required="in_array('address', $requiredInputs)" />
                            <x-form.text-input id="address" name="address" type="text" :value="$client->entity?->address" />
                            <x-form.input-error :messages="$errors->get('address')" />
                        </div>

                        <!-- City -->
                        <div>
                            <x-form.input-label for="city" :value="__('City')" :required="in_array('city', $requiredInputs)" />
                            <x-form.text-input id="city" name="city" type="text" :value="$client->entity?->city" />
                            <x-form.input-error :messages="$errors->get('city')" />
                        </div>

                        <!-- Postal Code -->
                        <div>
                            <x-form.input-label for="postal_code" :value="__('Postal Code')" :required="in_array('postal_code', $requiredInputs)" />
                            <x-form.text-input id="postal_code" name="postal_code" type="text" :value="$client->entity?->postal_code" />
                            <x-form.input-error :messages="$errors->get('postal_code')" />
                        </div>

                        <!-- Country -->
                        <div>
                            <x-form.input-label for="country" :value="__('Country')" :required="in_array('country', $requiredInputs)" />
                            <x-form.text-input id="country" name="country" type="text" :value="$client->entity?->country" />
                            <x-form.input-error :messages="$errors->get('country')" />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-form.input-label for="email" :value="__('Email')" :required="in_array('email', $requiredInputs)" />
                            <x-form.text-input id="email" name="email" type="email" :value="$client->email" />
                            <x-form.input-error :messages="$errors->get('email')" />
                        </div>

                        <!-- Due Days -->
                        <div>
                            <x-form.input-label for="due_days" :value="__('Due Days')" :required="in_array('due_days', $requiredInputs)" />
                            <x-form.text-input id="due_days" name="due_days" type="number" step="1"
                                :value="$client->due_days" autocomplete="due_days" />
                            <x-form.input-error :messages="$errors->get('due_days')" />
                        </div>

                        <!-- Discount Rate -->
                        <div>
                            <x-form.input-label for="discount_rate" :value="__('Discount Rate (%)')" :required="in_array('discount_rate', $requiredInputs)" />
                            <x-form.text-input id="discount_rate" name="discount_rate" type="number" step="0.01"
                                :value="$client->discount_rate" />
                            <x-form.input-error :messages="$errors->get('discount_rate')" />
                        </div>

                        <!-- Note -->
                        <div class="col-span-2">
                            <x-form.input-label for="note" :value="__('Note')" :required="in_array('note', $requiredInputs)" />
                            <x-form.textarea-input id="note" name="note" rows="2" :value="$client->entity?->note" />
                            <x-form.input-error :messages="$errors->get('note')" />
                        </div>
                    </div>

                    <!-- Bank Accounts Section -->
                    <x-bank-account-manager :banks="$banks ?? collect()" :bank-accounts="$client->entity->bankAccounts ?? collect()" />

                    <div class="flex items-center gap-4">
                        <x-buttons.secondary-button>
                            {{ $client->exists ? __('Update') : __('Create') }}
                        </x-buttons.secondary-button>

                        <x-links.light-link href="{{ route('cabinet.clients.index') }}">
                            {{ __('Cancel') }}
                        </x-links.light-link>
                    </div>
                </form>
            </section>
        </x-form.section>
    </x-form.section-container>
</x-app-layout>
