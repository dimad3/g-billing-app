<div class="col-span-2 mt-6" x-data="bankAccountManager({{ json_encode($bankAccounts) }})" x-init="init()">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-900">{{ __('Bank Accounts') }}</h3>
        <x-buttons.light-button x-on:click="addBankAccount">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ __('Add Bank Account') }}
        </x-buttons.light-button>
    </div>

    <!-- Bank account entries container -->
    <div id="bank-accounts-container" class="space-y-4 mt-4">
        <template x-for="(account, index) in accounts" :key="index">
            <div class="bank-account-item bg-gray-50 p-4 rounded-md border border-gray-200 relative">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Bank Select -->
                    <div>
                        <x-form.input-label x-bind:for="'bank_id_' + index" :value="__('Bank')" :required="true" />
                        <x-form.select-input x-bind:id="'bank_id_' + index"
                            x-bind:name="'bank_accounts[' + index + '][bank_id]'" x-model="account.bank_id"
                            :options="$banks->mapWithKeys(fn($bank) => [$bank->id => $bank->name])->toArray()" />
                        <p x-show="getError(index, 'bank_id')" x-text="getError(index, 'bank_id')"
                            class="text-sm text-red-600 space-y-1"></p>
                    </div>
                    <!-- Account Number Input with Remove Button -->
                    <div class="flex items-start">
                        <div class="flex-grow">
                            <x-form.input-label x-bind:for="'bank_account_' + index" :value="__('Account Number')"
                                :required="true" />
                            <x-form.text-input x-bind:id="'bank_account_' + index"
                                x-bind:name="'bank_accounts[' + index + '][bank_account]'"
                                x-model="account.bank_account" type="text" />
                            <p x-show="getError(index, 'bank_account')" x-text="getError(index, 'bank_account')"
                                class="text-sm text-red-600 space-y-1"></p>
                        </div>
                        <div class="ml-2 flex-shrink-0 pt-6">
                            <x-buttons.remove-button x-on:click="removeAccount(index)" />
                        </div>
                    </div>
                    <!-- Hidden ID field for existing accounts -->
                    <input type="hidden" x-bind:name="'bank_accounts[' + index + '][id]'"
                        x-bind:value="account.id" />
                </div>
            </div>
        </template>
    </div>
</div>

<script>
    window.laravelErrors = @json($errors->toArray());

    // Preserve old input for bank accounts after validation failure
    window.oldBankAccounts = @json(old('bank_accounts', []));
</script>
