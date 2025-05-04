<?php

namespace App\Repositories;

use App\Models\User\Agent;
use App\Repositories\Interfaces\AgentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class AgentRepository implements AgentRepositoryInterface
{
    /** Get all models for the authenticated user. */
    public function getAllForUser(): Collection
    {
        return Agent::forUser()->get();
    }

    /** Find model by ID. */
    public function find(int $id): Agent
    {
        // return Agent::forUser()->find($id);
        return Agent::forUser()->findOrFail($id);
    }

    /** Create a new model. */
    public function create(array $data): Agent
    {
        $data['user_id'] = Auth::id();

        return Agent::create($data);
    }

    /** Update model by ID. */
    public function updateById(int $id, array $data): Agent
    {
        $model = $this->find($id);

        return $this->update($model, $data);
    }

    /** Update model. */
    public function update(Agent $model, array $data): Agent
    {
        $model->update($data);

        return $model;
    }

    /** Delete model by ID. */
    public function deleteById(int $id): bool
    {
        return $this->find($id)->delete();
    }

    /** Delete model. */
    public function delete(Agent $model): bool
    {
        return $model->delete();
    }
}
