<?php

namespace App\Policies;

use App\Models\User\Agent;
use App\Models\User\User;
use Illuminate\Auth\Access\Response;

class AgentPolicy
{
    public function manageAgent(User $user, Agent $agent): Response
    {
        return $user->id === $agent->user_id
            ? Response::allow()
            : Response::denyWithStatus(404);
    }
}
