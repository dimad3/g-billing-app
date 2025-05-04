<?php

namespace App\Policies;

use App\Models\User\Client;
use App\Models\User\User;
use Illuminate\Auth\Access\Response;

class ClientPolicy
{
    public function manageClient(User $user, Client $client): Response
    {
        return $user->id === $client->user_id
            ? Response::allow()
            : Response::denyWithStatus(404);
    }
}
