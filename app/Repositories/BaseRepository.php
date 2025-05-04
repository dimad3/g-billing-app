<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    // public function find(int $id): ?Model
    // {
    //     return $this->model->find($id);
    // }

    // public function create(array $data): Model
    // {
    //     return $this->model->create($data);
    // }

    public function update(int $id, array $data): bool
    {
        $model = $this->find($id);
        return $model ? $model->update($data) : false;
    }

    public function delete(int $id): bool
    {
        return $this->model->destroy($id) > 0;
    }
}
