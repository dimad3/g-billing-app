<?php

namespace App\Repositories;

use App\Models\Entity\Entity;
use App\Models\User\Client;
use App\Models\User\User;
use App\Repositories\Interfaces\EntityRepositoryInterface;

class EntityRepository implements EntityRepositoryInterface
{
    /** Find model by ID. */
    public function find(int $id): ?Entity
    {
        $model = Entity::find($id);
        if (! $model) {
            throw new \Exception("Model not found.");
        }

        return $model;
    }

    /** Create a new model. */
    public function create(Client|User $entityable, array $data): Entity
    {
        $model = new Entity();
        $this->setEntityData($model, $data);

        $entityable->entity()->save($model);

        return $model;
    }

    /** Create a new model for a client. */
    public function createForClient(Client $client, array $data): Entity
    {
        $model = new Entity();
        $this->setEntityData($model, $data);

        $client->entity()->save($model);

        return $model;
    }

    /** Create a new model for a user. */
    public function createForUser(User $user, array $data): Entity
    {
        $model = new Entity();
        $this->setEntityData($model, $data);

        $user->entity()->save($model);

        return $model;
    }

    /** Update model by ID. */
    public function updateById(int $id, array $data): Entity
    {
        $model = $this->find($id);
        if (! $model) {
            throw new \Exception("Model not found.");
        }

        $this->setEntityData($model, $data);
        $model->save();

        return $model;
    }

    /** Update model. */
    public function update(Entity $model, array $data): Entity
    {
        $this->setEntityData($model, $data);
        $model->save();
        return $model;
    }

    /** Delete model by ID. */
    public function deleteById(int $id): bool
    {
        $model = $this->find($id);
        if (! $model) {
            throw new \Exception("Model not found.");
        }

        return $model->delete();
    }

    /** Delete model. */
    public function delete(Entity $model): bool
    {
        return $model->delete();
    }

    /** Set model data from array. */
    protected function setEntityData(Entity $model, array $data): void
    {
        $model->entity_type = $data['entity_type'];
        $model->legal_form = $data['legal_form'];

        // Set name fields based on user type
        if ($data['entity_type'] === 'legal_entity') {
            $model->name = $data['name'];
            $model->first_name = null;
            $model->last_name = null;
        } else {
            $model->name = null;
            $model->first_name = $data['first_name'];
            $model->last_name = $data['last_name'];
        }

        $model->id_number = $data['id_number'];
        $model->vat_number = $data['vat_number'] ?? null;
        $model->address = $data['address'] ?? null;
        $model->city = $data['city'] ?? null;
        $model->postal_code = $data['postal_code'] ?? null;
        $model->country = $data['country'] ?? null;
        $model->note = $data['note'] ?? null;
    }
}
