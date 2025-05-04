<?php

namespace App\Repositories\Interfaces;

use App\Models\User\Bank;
use Illuminate\Support\Collection;

interface BankRepositoryInterface
{
    /** Get all banks for the authenticated user. */
    public function getAllForUser(): Collection;

    /** Find bank by ID. */
    public function find(int $id): Bank;

    /** Create a new bank. */
    public function create(array $data): Bank;

    /** Update bank by ID. */
    public function updateById(int $id, array $data): Bank;

    /** Update bank. */
    public function update(Bank $bank, array $data): Bank;

    /** Delete bank by ID. */
    public function deleteById(int $id): bool;

    /** Delete bank. */
    public function delete(Bank $bank): bool;
}
