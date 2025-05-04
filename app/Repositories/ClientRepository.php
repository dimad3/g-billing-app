<?php

namespace App\Repositories;

use App\Models\User\Client;
use App\Repositories\Interfaces\ClientRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class ClientRepository implements ClientRepositoryInterface
{
    /** Get all models for the authenticated user. */
    public function getAllForUser(): Collection
    {
        return Client::forUser()->get();
    }

    /** Find model by ID. */
    public function find(int $id): ?Client
    {
        $model = Client::find($id);
        if (! $model) {
            throw new \Exception("Model not found.");
        }

        return $model;
    }

    /** Create a new model. */
    public function create(array $data): Client
    {
        $model = new Client();
        $model->user_id = Auth::id();
        $model->email = $data['email'];
        $model->due_days = $data['due_days'];
        $model->discount_rate = $data['discount_rate'];
        $model->save();

        return $model;
    }

    /** Update model by ID. */
    public function updateById(int $id, array $data): Client
    {
        $model = $this->find($id);
        if (! $model) {
            throw new \Exception("Model not found.");
        }

        $model->email = $data['email'];
        $model->due_days = $data['due_days'];
        $model->discount_rate = $data['discount_rate'];
        $model->save();

        return $model;
    }

    /** Update model. */
    public function update(Client $model, array $data): Client
    {
        $model->email = $data['email'];
        $model->due_days = $data['due_days'];
        $model->discount_rate = $data['discount_rate'];
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
    public function delete(Client $model): bool
    {
        return $model->delete();
    }
}
