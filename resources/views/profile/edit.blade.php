<x-app-layout title="{{ __('User Profile') }}">
    @include('profile.partials._nav')

    <x-form.section-container>
        <x-form.section>
            @include('profile.partials.update-profile-information-form')
        </x-form.section>

        <x-form.section>
            @include('profile.partials.update-password-form')
        </x-form.section>

        <x-form.section>
            @include('profile.partials.delete-user-form')
        </x-form.section>
    </x-form.section-container>
</x-app-layout>
