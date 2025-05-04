<?php

namespace App\Repositories\Interfaces;

use App\Models\User\Agent;
use Illuminate\Support\Collection;

interface AgentRepositoryInterface
{
    /** Get all agents for the authenticated user. */
    public function getAllForUser(): Collection;

    /** Find agent by ID. */
    public function find(int $id): Agent;

    /** Create a new agent. */
    public function create(array $data): Agent;

    /** Update agent by ID. */
    public function updateById(int $id, array $data): Agent;

    /** Update agent. */
    public function update(Agent $agent, array $data): Agent;

    /** Delete agent by ID. */
    public function deleteById(int $id): bool;

    /** Delete agent. */
    public function delete(Agent $agent): bool;
}
