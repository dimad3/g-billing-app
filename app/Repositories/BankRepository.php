<?php

namespace App\Repositories;

use App\Models\User\Bank;
use App\Repositories\Interfaces\BankRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class BankRepository implements BankRepositoryInterface
{
    /** Get all models for the authenticated user. */
    public function getAllForUser(): Collection
    {
        return Bank::forUser()->get();
    }

    /** Find model by ID. */
    public function find(int $id): Bank
    {
        // return Bank::forUser()->find($id);
        return Bank::forUser()->findOrFail($id);
    }

    /** Create a new model. */
    public function create(array $data): Bank
    {
        $data['user_id'] = Auth::id();

        return Bank::create($data);
    }

    /** Update model by ID. */
    public function updateById(int $id, array $data): Bank
    {
        $model = $this->find($id);

        return $this->update($model, $data);
    }

    /** Update model. */
    public function update(Bank $model, array $data): Bank
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
    public function delete(Bank $model): bool
    {
        return $model->delete();
    }
}
