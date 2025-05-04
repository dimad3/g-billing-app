<?php

namespace App\Models\Document;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentItem extends Model
{
    use HasFactory;

    protected float $calculatedAmount = 0;
    protected float $calculatedDiscount = 0;

    protected $guarded = ['id'];

    protected $casts = [
        // 'quantity' => 'decimal:3',
        // 'price' => 'decimal:5',
        // 'discount_rate' => 'decimal:2',
        // 'tax_rate' => 'decimal:2',
    ];

    protected $appends = [
        'amount',
        'discount',
        'net_amount',
    ];

    // Accessors ******************************************************************

    protected function amount(): Attribute
    {
        return Attribute::get(function () {
            return $this->calculatedAmount = round($this->quantity * $this->price, 2);
        });
    }

    protected function discount(): Attribute
    {
        return Attribute::get(function () {
            $amount = $this->calculatedAmount ?? round($this->quantity * $this->price, 2);
            return $this->calculatedDiscount = round(($amount * $this->discount_rate) / 100, 2);
        });
    }

    protected function netAmount(): Attribute
    {
        return Attribute::get(function () {
            $amount = $this->calculatedAmount ?? round($this->quantity * $this->price, 2);
            $discount = $this->calculatedDiscount ?? round(($amount * $this->discount_rate) / 100, 2);
            return round($amount - $discount, 2);
        });
    }

    // Mutators ******************************************************************

    // Mutator for quantity
    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = $this->formatDecimal($value, 3);
    }

    // Mutator for price
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $this->formatDecimal($value, 5);
    }

    // Mutator for discount_rate
    public function setDiscountRateAttribute($value)
    {
        $this->attributes['discount_rate'] = $this->formatDecimal($value, 2);
    }

    // Mutator for tax_rate
    public function setTaxRateAttribute($value)
    {
        $this->attributes['tax_rate'] = $this->formatDecimal($value, 2);
    }

    // Shared method for formatting decimal values
    private function formatDecimal($value, $precision = 5)
    {
        return is_numeric($value) ? round($value, $precision) : 0;
    }

    // Relations ******************************************************************

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
