<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-form.input-label for="password" :value="__('Password')" />

            <x-form.text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-form.input-error :messages="$errors->get('password')"  />
        </div>

        <div class="flex justify-end mt-4">
            <x-buttons.secondary-button>
                {{ __('Confirm') }}
            </x-buttons.secondary-button>
        </div>
    </form>
</x-guest-layout>
