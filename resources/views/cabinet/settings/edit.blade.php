<x-app-layout title="{{ __('Update Settings') }}">
    @include('cabinet.settings._nav')
    <x-form.section-container>
        <x-form.section>
            <section>
                <x-form.section-header>
                    <x-slot:title>
                        {{ __('Settings') }}
                    </x-slot>

                    {{ $settings->exists ? __('Update Settings.') : __('Fill in Information.') }}
                </x-form.section-header>

                <form
                    action="{{ $settings->exists ? route('cabinet.settings.update', $settings) : route('cabinet.settings.store') }}"
                    method="POST" class="space-y-6">
                    @if ($settings->exists)
                        @method('PUT')
                    @endif
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Number Prefix -->
                        <div>
                            <x-form.input-label for="number_prefix" :value="__('Number Prefix')" :required="in_array('number_prefix', $requiredInputs)" />
                            <x-form.text-input id="number_prefix" name="number_prefix" type="text" :value="$settings?->number_prefix" />
                            <x-form.input-error :messages="$errors->get('number_prefix')" />
                        </div>

                        <!-- Next Number -->
                        <div>
                            <x-form.input-label for="next_number" :value="__('Next Number')" :required="in_array('next_number', $requiredInputs)" />
                            <x-form.text-input id="next_number" name="next_number" type="text" :value="$settings?->next_number" />
                            <x-form.input-error :messages="$errors->get('next_number')" />
                        </div>

                        <!-- Default Employee -->
                        <div>
                            <x-form.input-label for="default_agent_id" :value="__('Default Employee')" :required="in_array('default_agent_id', $requiredInputs)" />
                            <x-form.select-input id="default_agent_id" name="default_agent_id" {{-- Dynamic variables has `:` --}}
                                :options="$agents" :value="$settings->default_agent_id" {{-- For Static strings `:` is omited --}} keyField="id"
                                labelField="fullName" />
                            <x-form.input-error :messages="$errors->get('default_agent_id')" />
                        </div>

                        <!-- Default Vat Rate -->
                        <div>
                            <x-form.input-label for="default_tax_rate" :value="__('Default Vat Rate')" :required="in_array('default_tax_rate', $requiredInputs)" />
                            <x-form.text-input id="default_tax_rate" name="default_tax_rate" type="text" :value="$settings?->default_tax_rate" />
                            <x-form.input-error :messages="$errors->get('default_tax_rate')" />
                        </div>
                    </div>

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
