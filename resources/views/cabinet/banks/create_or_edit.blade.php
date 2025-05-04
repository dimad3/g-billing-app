<x-app-layout title="{{ $bank->exists ? __('Update Bank') : __('Add New Bank') }}">
    @include('cabinet.banks._nav')
    <x-form.section-container>
        <x-form.section>
            <section>
                <x-form.section-header>
                    <x-slot:title>
                        {{ $bank->exists ? __('Bank Information') : __('Add New Bank') }}
                    </x-slot>

                    {{ $bank->exists ? __('Update bank information.') : __('Fill in bank information.') }}
                </x-form.section-header>

                <form
                    action="{{ $bank->exists ? route('cabinet.banks.update', $bank) : route('cabinet.banks.store') }}"
                    method="POST" class="space-y-6">
                    @if ($bank->exists)
                        @method('PUT')
                    @endif
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div id="name_field">
                            <x-form.input-label for="name" :value="__('Bank Name')" :required="in_array('name', $requiredInputs)" />
                            <x-form.text-input id="name" name="name" type="text" :value="$bank?->name" />
                            <x-form.input-error :messages="$errors->get('name')" />
                        </div>

                        <!-- Bank Code -->
                        <div id="bank_code_field">
                            <x-form.input-label for="bank_code" :value="__('Bank Code')" :required="in_array('bank_code', $requiredInputs)" />
                            <x-form.text-input id="bank_code" name="bank_code" type="text" :value="$bank?->bank_code" />
                            <x-form.input-error :messages="$errors->get('bank_code')" />
                        </div>

                    </div>

                    <div class="flex items-center gap-4">
                        <x-buttons.secondary-button>
                            {{ $bank->exists ? __('Update') : __('Create') }}
                        </x-buttons.secondary-button>

                        <x-links.light-link href="{{ route('cabinet.banks.index') }}">
                            {{ __('Cancel') }}
                        </x-links.light-link>
                    </div>
                </form>
            </section>
        </x-form.section>
    </x-form.section-container>
</x-app-layout>
