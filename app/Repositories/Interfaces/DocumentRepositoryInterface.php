<?php

namespace App\Repositories\Interfaces;

use App\Models\Document\Document;
use Illuminate\Support\Collection;

interface DocumentRepositoryInterface
{
    /** Get all models for the authenticated user. */
    public function getAllForUser(): Collection;

    /** Find model by ID. */
    public function find(int $id): Document;

    /** Create a new model. */
    public function create(array $data): Document;

    /** Update model by ID. */
    public function updateById(int $id, array $data): Document;

    /** Update model. */
    public function update(Document $document, array $data): Document;

    /** Delete model by ID. */
    public function deleteById(int $id): bool;

    /** Delete model. */
    public function delete(Document $document): bool;
}
