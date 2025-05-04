<?php

namespace App\Policies;

use App\Models\User\DocumentSetting;
use App\Models\User\User;
use Illuminate\Auth\Access\Response;

class DocumentSettingPolicy
{
    public function manageDocumentSetting(User $user, DocumentSetting $settings): Response
    {
        return $user->id === $settings->user_id
            ? Response::allow()
            : Response::denyWithStatus(404);
    }
}
