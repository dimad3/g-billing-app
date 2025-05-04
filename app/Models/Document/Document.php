<?php

namespace App\Models\Document;

use App\Models\User\Agent;
use App\Models\User\Client;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Document extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // 'document_date' => 'date:Y-m-d',
            // 'due_date' => 'date:Y-m-d',
            // 'delivery_date' => 'date:Y-m-d',
            // 'advance_paid' => 'decimal:2',
            'show_created_by' => 'boolean',
            'show_signature' => 'boolean',
        ];
    }

    public function setDocumentDateAttribute($value)
    {
        $this->attributes['document_date'] = Carbon::parse($value)->format('Y-m-d');
    }

    public function setDueDateAttribute($value)
    {
        $this->attributes['due_date'] = Carbon::parse($value)->format('Y-m-d');
    }

    public function setDeliveryDateAttribute($value)
    {
        $value === null ? $this->attributes['delivery_date'] = null : $this->attributes['delivery_date'] = Carbon::parse($value)->format('Y-m-d');
    }

    // Accessors ******************************************************************

    // public function getDocumentWithNumberAttribute()
    // {
    //     // Remove any null, false, or empty strings from the array
    //     return implode(', ', array_filter([
    //         $this->document_type,
    //         ' Nr. ',
    //         $this->number,
    //     ]));
    // }

    // Mutators ******************************************************************

    public function setAdvancePaidAttribute($value)
    {
        $this->attributes['advance_paid'] = round($value, 2);
    }

    // Relations ******************************************************************

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function documentItems(): HasMany
    {
        return $this->hasMany(DocumentItem::class);
    }

    // Scopes ******************************************************************

    public function scopeForUser(Builder $query): void
    {
        $query->where('user_id', auth()->user()->id);
    }
}
