<?php

namespace App\Services;

use App\Models\User\Agent;
use App\Repositories\Interfaces\AgentRepositoryInterface;

class AgentService
{
    public function __construct(
        protected AgentRepositoryInterface $agentRepository,
    ) {}

    /** Create a new agent with related data. */
    public function createAgent(array $data): Agent
    {
        $agent = $this->agentRepository->create($data);

        return $agent;
    }

    /** Update an existing agent with related data. */
    public function updateAgent(array $data, Agent $agent): Agent
    {
        $agent = $this->agentRepository->update($agent, $data);

        return $agent;
    }

    /** Delete a agent. */
    public function deleteAgent(Agent $agent): bool
    {
        $deleted = $this->agentRepository->delete($agent);

        return $deleted;
    }
}
