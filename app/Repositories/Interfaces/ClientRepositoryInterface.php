<?php

namespace App\Repositories\Interfaces;

use App\Models\User\Client;
use Illuminate\Support\Collection;

interface ClientRepositoryInterface
{
    /** Get all clients for the authenticated user. */
    public function getAllForUser(): Collection;

    /** Find client by ID. */
    public function find(int $id): ?Client;

    /** Create a new client. */
    public function create(array $data): Client;

    /** Update client by ID. */
    public function updateById(int $id, array $data): Client;

    /** Update client. */
    public function update(Client $client, array $data): Client;

    /** Delete client by ID. */
    public function deleteById(int $id): bool;

    /** Delete client. */
    public function delete(Client $client): bool;
}
