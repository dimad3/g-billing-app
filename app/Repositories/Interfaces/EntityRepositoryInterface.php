<?php

namespace App\Repositories\Interfaces;

use App\Models\Entity\Entity;
use App\Models\User\Client;
use App\Models\User\User;
use Illuminate\Support\Collection;

interface EntityRepositoryInterface
{
    /** Find entity by ID. */
    public function find(int $id): ?Entity;

    /** Create a new entity. */
    public function create(Client|User $entityable, array $data): Entity;

    /** Create a new entity for a client. */
    public function createForClient(Client $client, array $data): Entity;

    /** Create a new entity for a user. */
    public function createForUser(User $user, array $data): Entity;

    /** Update entity by ID. */
    public function updateById(int $id, array $data): Entity;

    /** Update entity. */
    public function update(Entity $entity, array $data): Entity;

    /** Delete entity by ID. */
    public function deleteById(int $id): bool;

    /** Delete entity. */
    public function delete(Entity $entity): bool;
}
