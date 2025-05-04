<?php

namespace App\Services;

use App\Models\User\Bank;
use App\Repositories\Interfaces\BankRepositoryInterface;

class BankService
{
    public function __construct(
        protected BankRepositoryInterface $bankRepository,
    ) {}

    /** Create a new bank with related data. */
    public function createBank(array $data): Bank
    {
        $bank = $this->bankRepository->create($data);

        return $bank;
    }

    /** Update an existing bank with related data. */
    public function updateBank(array $data, Bank $bank): Bank
    {
        $bank = $this->bankRepository->update($bank, $data);

        return $bank;
    }

    /** Delete a bank. */
    public function deleteBank(Bank $bank): bool
    {
        $deleted = $this->bankRepository->delete($bank);

        return $deleted;
    }
}
