<?php

namespace App\Models\Entity;

use App\Models\User\Bank;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntityBankAccount extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }
}
