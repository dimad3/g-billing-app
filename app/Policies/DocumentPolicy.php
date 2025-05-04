<?php

namespace App\Policies;

use App\Models\Document\Document;
use App\Models\User\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
{
    public function manageDocument(User $user, Document $document): Response
    {
        return $user->id === $document->user_id
            ? Response::allow()
            : Response::denyWithStatus(404);
    }
}
