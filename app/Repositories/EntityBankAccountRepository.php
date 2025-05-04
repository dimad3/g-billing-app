<?php

namespace App\Repositories;

use App\Models\Entity\Entity;
use App\Models\Entity\EntityBankAccount;
use App\Repositories\Interfaces\EntityBankAccountRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EntityBankAccountRepository implements EntityBankAccountRepositoryInterface
{
    /** @var EntityBankAccount */
    protected $model;

    public function __construct(EntityBankAccount $bankAccount)
    {
        $this->model = $bankAccount;
    }

    /** Find bank account by ID. */
    public function find(int $id): ?EntityBankAccount
    {
        return $this->model->find($id);
    }

    /** Get all bank accounts for an entity. */
    public function getAllForEntity(Entity $entity): Collection
    {
        return $entity->bankAccounts;
    }

    /** Create a new bank account for an entity. */
    public function create(Entity $entity, array $data): EntityBankAccount
    {
        $bankAccount = new EntityBankAccount();
        $bankAccount->entity_id = $entity->id;
        $bankAccount->bank_id = $data['bank_id'];
        $bankAccount->bank_account = $data['bank_account'];
        $bankAccount->save();

        return $bankAccount;
    }

    /** Update a bank account. */
    public function update(EntityBankAccount $bankAccount, array $data): EntityBankAccount
    {
        $bankAccount->bank_id = $data['bank_id'];
        $bankAccount->bank_account = $data['bank_account'];
        $bankAccount->save();

        return $bankAccount;
    }

    /** Delete any bank accounts for an entity that aren't in the provided IDs array. */
    public function deleteExcept(Entity $entity, array $keepIds): bool
    {
        return $entity->bankAccounts()->whereNotIn('id', $keepIds)->delete();
    }

    /** Delete all bank accounts for an entity. */
    public function deleteAllForEntity(Entity $entity): bool
    {
        return $entity->bankAccounts()->delete();
    }
}
