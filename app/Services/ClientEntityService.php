<?php

namespace App\Services;

use App\Models\Entity\Entity;
use App\Models\User\Client;
use App\Repositories\Interfaces\ClientRepositoryInterface;
use App\Repositories\Interfaces\EntityBankAccountRepositoryInterface;
use App\Repositories\Interfaces\EntityRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ClientEntityService
{
    public function __construct(
        protected ClientRepositoryInterface $clientRepository,
        protected EntityRepositoryInterface $entityRepository,
        // protected EntityBankAccountRepositoryInterface $bankAccountRepository,
    ) {}

    /** Create a new client with related data. */
    public function createClient(array $data): Client
    {
        return DB::transaction(function () use ($data) {
            // Create client
            $client = $this->clientRepository->create($data);

            // Create entity
            $entity = $this->entityRepository->create($client, $data);

            // Create bank accounts
            ($bankAccounts = $data['bank_accounts']);
            // $this->processBankAccounts($bankAccounts, $entity);
            $entity->bankAccounts()->createMany($bankAccounts);

            return $client;
        });
    }

    /** Update an existing client with related data. */
    public function updateClient(array $data, Client $client): Client
    {
        return DB::transaction(function () use ($data, $client) {
            // Update client
            $client = $this->clientRepository->update($client, $data);

            // Update or create entity
            if ($client->entity) {
                $entity = $this->entityRepository->update($client->entity, $data);
            } else {
                $entity = $this->entityRepository->createForClient($client, $data);
            }

            // Update bank accounts
            $bankAccounts = $data['bank_accounts'];
            // $this->processBankAccounts($bankAccounts, $entity);
            $entity->bankAccounts()->delete();
            $entity->bankAccounts()->createMany($bankAccounts);

            return $client;
        });
    }

    /** Delete a client. */
    public function deleteClient(Client $client): bool
    {

        // If client has documents, prevent deletion
        // if ($client->documents()->exists()) {
        //     throw new \Exception(__("Cannot delete client because it has associated documents."));
        // }

        // Proceed with deletion
        return DB::transaction(function () use ($client) {
            $this->entityRepository->delete($client->entity);
            $deleted = $this->clientRepository->delete($client);

            return $deleted;
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
