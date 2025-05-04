<?php

namespace App\Models\User;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agent extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * @var array The accessors to append to the model's array form.
     */
    protected $appends = [
        'full_name',
        'full_name_reversed',
    ];

    // Relations ******************************************************************

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors ******************************************************************

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name} ";
    }

    public function getFullNameReversedAttribute()
    {
        return "{$this->last_name} {$this->first_name}";
    }

    // Scopes ******************************************************************

    public function scopeForUser(Builder $query): void
    {
        $query->where('user_id', auth()->user()->id);
    }
}
