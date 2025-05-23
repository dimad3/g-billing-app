<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-form.input-label for="name" :value="__('Name')" />
            <x-form.text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-form.input-error :messages="$errors->get('name')"  />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-form.input-label for="email" :value="__('Email')" />
            <x-form.text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-form.input-error :messages="$errors->get('email')"  />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-form.input-label for="password" :value="__('Password')" />

            <x-form.text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-form.input-error :messages="$errors->get('password')"  />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-form.input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-form.text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-form.input-error :messages="$errors->get('password_confirmation')"  />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-buttons.secondary-button class="ms-4">
                {{ __('Register') }}
            </x-buttons.secondary-button>
        </div>
    </form>
</x-guest-layout>
