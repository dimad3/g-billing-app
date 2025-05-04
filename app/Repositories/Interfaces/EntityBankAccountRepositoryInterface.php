<?php

namespace App\Repositories\Interfaces;

use App\Models\Entity\Entity;
use App\Models\Entity\EntityBankAccount;
use Illuminate\Support\Collection;

interface EntityBankAccountRepositoryInterface
{
    /** Find bank account by ID. */
    public function find(int $id): ?EntityBankAccount;

    /** Get all bank accounts for an entity. */
    public function getAllForEntity(Entity $entity): Collection;

    /** Create a new bank account for an entity. */
    public function create(Entity $entity, array $data): EntityBankAccount;

    /** Update a bank account. */
    public function update(EntityBankAccount $bankAccount, array $data): EntityBankAccount;

    /** Delete bank accounts not in the given list of IDs. */
    public function deleteExcept(Entity $entity, array $keepIds): bool;

    /** Delete all bank accounts for an entity. */
    public function deleteAllForEntity(Entity $entity): bool;
}
