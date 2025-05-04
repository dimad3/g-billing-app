<?php

namespace App\Models\User;

use App\Models\Document\Document;
use App\Models\Entity\Entity;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Client extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        // 'discount_rate' => 'decimal:2',
        // 'due_days' => 'integer',
    ];

    /**
     * The "boot" method is used to define model event hooks.
     * This method is automatically called when the model is initialized.
     * You can use this method to register event listeners such as creating, updating, deleting, etc.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($client) {
            if ($client->documents()->exists()) {
                throw new \Exception("Cannot delete client because it has associated documents.");
            }
        });
    }

    // Relations ******************************************************************

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function entity(): MorphOne
    {
        return $this->morphOne(Entity::class, 'entityable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes ******************************************************************

    public function scopeForUser(Builder $query): void
    {
        $query->where('user_id', auth()->user()->id);
    }
}
