<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Collection;

/**
 * Handles the logic for the bank account manager
 * Takes the required collections for banks and existing bank accounts
 */

class BankAccountManager extends Component
{
    public Collection $banks;
    public Collection $bankAccounts;
    public bool $hasExistingAccounts;

    /**
     * Create a new component instance.
     *
     * @param Collection $banks
     * @param Collection|null $bankAccounts
     * @return void
     */
    public function __construct(Collection $banks, ?Collection $bankAccounts = null)
    {
        $this->banks = $banks;
        $this->bankAccounts = $bankAccounts ?? collect();
        $this->hasExistingAccounts = $this->bankAccounts->isNotEmpty();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.bank-account-manager');
    }
}
