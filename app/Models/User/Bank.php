<?php

namespace App\Models\User;

use App\Models\Entity\EntityBankAccount;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bank extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public $timestamps = false;

    /** @var array The accessors to append to the model's array form. */
    protected $appends = [
        'fullName',
    ];

    // Relations ******************************************************************

    public function entityBankAccounts(): HasMany
    {
        return $this->hasMany(EntityBankAccount::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors ******************************************************************

    public function getFullNameAttribute()
    {
        // Remove any null, false, or empty strings from the array
        return implode('; ', array_filter([
            $this->name,
            'code: ' . $this->bank_code,
        ]));
    }

    // Scopes ******************************************************************

    public function scopeForUser(Builder $query): void
    {
        $query->where('user_id', auth()->user()->id);
    }
}
