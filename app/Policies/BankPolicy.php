<?php

namespace App\Policies;

use App\Models\User\Bank;
use App\Models\User\User;
use Illuminate\Auth\Access\Response;

class BankPolicy
{
    public function manageBank(User $user, Bank $bank): Response
    {
        return $user->id === $bank->user_id
            ? Response::allow()
            : Response::denyWithStatus(404);
    }
}
