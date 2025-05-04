<?php

namespace App\Services;

use App\Models\Entity\Entity;
use App\Models\User\Client;
use App\Repositories\Interfaces\ClientRepositoryInterface;
use App\Repositories\Interfaces\EntityBankAccountRepositoryInterface;
use App\Repositories\Interfaces\EntityRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SellerEntityService
{
    public function __construct(
        // protected ClientRepositoryInterface $clientRepository,
        protected EntityRepositoryInterface $entityRepository,
        // protected EntityBankAccountRepositoryInterface $bankAccountRepository,
    ) {}

    /** Create a new seller with related data. */
    public function createEntity(array $data): Entity
    {
        return DB::transaction(function () use ($data) {
            // Create entity
            $entity = $this->entityRepository->createForUser(auth()->user(), $data);

            // Create bank accounts
            ($bankAccounts = $data['bank_accounts']);
            // $this->processBankAccounts($bankAccounts, $entity);
            $entity->bankAccounts()->createMany($bankAccounts);

            return $entity;
        });
    }

    /** Update an existing seller with related data. */
    public function updateEntity(array $data, Entity $entity): Entity
    {
        return DB::transaction(function () use ($data, $entity) {
            // Update seller
            $entity = $this->entityRepository->update($entity, $data);

            // Update bank accounts
            $bankAccounts = $data['bank_accounts'];
            // $this->processBankAccounts($bankAccounts, $entity);
            $entity->bankAccounts()->delete();
            $entity->bankAccounts()->createMany($bankAccounts);

            return $entity;
        });
    }

    /** Process bank accounts from request. */
    // protected function processBankAccounts(array $bankAccounts = [], Entity $entity): void
    // {
    //     // $bankAccounts = $request->input('bank_accounts', []);
    //     $existingIds = [];

    //     foreach ($bankAccounts as $data) {
    //         if (! empty($data['bank_id']) && ! empty($data['bank_account'])) {
    //             if (isset($data['id'])) {
    //                 // Update existing account
    //                 $account = $this->bankAccountRepository->find($data['id']);
    //                 if ($account && $account->entity_id === $entity->id) {
    //                     $this->bankAccountRepository->update($account, $data);
    //                     $existingIds[] = $account->id;
    //                 }
    //             } else {
    //                 // Create new account
    //                 $account = $this->bankAccountRepository->create($entity, $data);
    //                 $existingIds[] = $account->id;
    //             }
    //         }
    //     }

    //     // Delete any bank accounts that were removed
    //     if (! empty($existingIds)) {
    //         $this->bankAccountRepository->deleteExcept($entity, $existingIds);
    //     }
    //     // else if ($request->has('bank_accounts')) {
    //     //     // If bank_accounts is in the request but there are no valid accounts, delete all
    //     //     $this->bankAccountRepository->deleteAllForEntity($entity);
    //     // }
    // }
}
