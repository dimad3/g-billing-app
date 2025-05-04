<section>
    <x-form.section-header>
        <x-slot:title>
            {{ __('Update Password') }}
        </x-slot>

        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </x-form.section-header>


    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-form.input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-form.text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-form.input-error :messages="$errors->updatePassword->get('current_password')"  />
        </div>

        <div>
            <x-form.input-label for="update_password_password" :value="__('New Password')" />
            <x-form.text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-form.input-error :messages="$errors->updatePassword->get('password')"  />
        </div>

        <div>
            <x-form.input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-form.text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-form.input-error :messages="$errors->updatePassword->get('password_confirmation')"  />
        </div>

        <div class="flex items-center gap-4">
            <x-buttons.secondary-button>{{ __('Save') }}</x-buttons.secondary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
