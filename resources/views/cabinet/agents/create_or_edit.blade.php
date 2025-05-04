<x-app-layout title="{{ $agent->exists ? __('Update Employee') : __('Add New Employee') }}">
    @include('cabinet.agents._nav')
    <x-form.section-container>
        <x-form.section>
            <section>
                <x-form.section-header>
                    <x-slot:title>
                        {{ $agent->exists ? __('Employee Information') : __('Add New Employee') }}
                    </x-slot>

                    {{ $agent->exists ? __('Update employee information.') : __('Fill in employee information.') }}
                </x-form.section-header>

                <form
                    action="{{ $agent->exists ? route('cabinet.agents.update', $agent) : route('cabinet.agents.store') }}"
                    method="POST" class="space-y-6">
                    @if ($agent->exists)
                        @method('PUT')
                    @endif
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div id="first_name_field">
                            <x-form.input-label for="first_name" :value="__('First Name')" :required="in_array('first_name', $requiredInputs)" />
                            <x-form.text-input id="first_name" name="first_name" type="text" :value="$agent?->first_name" />
                            <x-form.input-error :messages="$errors->get('first_name')" />
                        </div>

                        <!-- Last Name -->
                        <div id="last_name_field">
                            <x-form.input-label for="last_name" :value="__('Last Name')" :required="in_array('last_name', $requiredInputs)" />
                            <x-form.text-input id="last_name" name="last_name" type="text" :value="$agent?->last_name" />
                            <x-form.input-error :messages="$errors->get('last_name')" />
                        </div>

                        <!-- Position -->
                        <div>
                            <x-form.input-label for="position" :value="__('Position')" :required="in_array('position', $requiredInputs)" />
                            <x-form.text-input id="position" name="position" type="text" :value="$agent?->position" />
                            <x-form.input-error :messages="$errors->get('position')" />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-form.input-label for="email" :value="__('Email')" :required="in_array('email', $requiredInputs)" />
                            <x-form.text-input id="email" name="email" type="email" :value="$agent->email" />
                            <x-form.input-error :messages="$errors->get('email')" />
                        </div>

                        <!-- Role -->
                        <div>
                            <x-form.input-label for="role" :value="__('Role')" :required="in_array('role', $requiredInputs)" />
                            <x-form.select-input id="role" name="role" :options="$roles" :value="$agent?->role" />
                            <x-form.input-error :messages="$errors->get('role')" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-buttons.secondary-button>
                            {{ $agent->exists ? __('Update') : __('Create') }}
                        </x-buttons.secondary-button>

                        <x-links.light-link href="{{ route('cabinet.agents.index') }}">
                            {{ __('Cancel') }}
                        </x-links.light-link>
                    </div>
                </form>
            </section>
        </x-form.section>
    </x-form.section-container>
</x-app-layout>
