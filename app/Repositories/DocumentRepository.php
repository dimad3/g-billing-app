<?php

namespace App\Repositories;

use App\Models\Document\Document;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class DocumentRepository implements DocumentRepositoryInterface
{
    /** Get all models for the authenticated user. */
    public function getAllForUser(): Collection
    {
        return Document::forUser()->get();
    }

    /** Find model by ID. */
    public function find(int $id): Document
    {
        // return Document::forUser()->find($id);
        return Document::forUser()->findOrFail($id);
    }

    /** Create a new model. */
    public function create(array $data): Document
    {
        $data['user_id'] = Auth::id();

        return Document::create($data);
    }

    /** Update model by ID. */
    public function updateById(int $id, array $data): Document
    {
        $model = $this->find($id);

        return $this->update($model, $data);
    }

    /** Update model. */
    public function update(Document $model, array $data): Document
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
    public function delete(Document $model): bool
    {
        return $model->delete();
    }
}
