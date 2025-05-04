<?php

namespace App\Models\Entity;

use App\Models\User\Client;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Entity extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /** @var array The accessors to append to the model's array form. */
    protected $appends = [
        'fullName',
        'fullAddress',
        'type',
    ];

    // Relations ******************************************************************

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(EntityBankAccount::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'entityable_id');
    }

    public function entityable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'entityable_id');
    }

    // Accessors ******************************************************************

    public function getTypeAttribute()
    {
        return config("static_data.entity_types.{$this->entity_type}");
    }

    public function getFullNameAttribute()
    {
        $legalForm = config("static_data.legal_forms.{$this->entity_type}.{$this->legal_form}");

        if ($this->entity_type === 'individual') {
            $individualName = "{$this->last_name} {$this->first_name}";
            if ($this->legal_form === 'natural_person') {
                return $individualName;
            }
            // Remove any null, false, or empty strings from the array
            return implode(', ', array_filter([
                $individualName,
                $legalForm,
            ]));
        }
        // Remove any null, false, or empty strings from the array
        return implode(', ', array_filter([
            $this->name,
            $legalForm,
        ]));
    }

    public function getFullAddressAttribute()
    {
        // Remove any null, false, or empty strings from the array
        return implode(', ', array_filter([
            $this->address,
            $this->city,
            $this->country,
            $this->postal_code
        ]));
    }
}
