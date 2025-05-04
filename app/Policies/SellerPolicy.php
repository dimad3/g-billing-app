<?php

namespace App\Policies;

use App\Models\Entity\Entity;
use App\Models\User\User;
use Illuminate\Auth\Access\Response;

class SellerPolicy
{
    public function manageSeller(User $user, Entity $entity): Response
    {
        return $user->id === $entity->entityable_id
            ? Response::allow()
            : Response::denyWithStatus(404);
    }
}
