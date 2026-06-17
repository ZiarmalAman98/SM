<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InventorySupplier extends Model
{
    protected $fillable = [
        'supplier_code',
        'name',
        'company_name',
        'contact_person',
        'phone',
        'alternate_phone',
        'email',
        'address',
        'opening_balance',
        'opening_balance_type',
        'is_active',
        'tax_number',
        'bank_account',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (InventorySupplier $supplier): void {
            if (blank($supplier->supplier_code)) {
                $supplier->supplier_code = static::generateSupplierCode();
            }
        });
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(InventoryPurchase::class);
    }

    public function latestPaidPurchase(): HasOne
    {
        return $this->hasOne(InventoryPurchase::class)
            ->where('paid_amount', '>', 0)
            ->latestOfMany();
    }

    public function getPurchasesTotalAttribute(): float
    {
        return (float) ($this->purchases_total_amount
            ?? $this->purchases()->where('status', '!=', 'cancelled')->sum('total_amount'));
    }

    public function getPurchasesPaidAttribute(): float
    {
        return (float) ($this->purchases_paid_amount
            ?? $this->purchases()->where('status', '!=', 'cancelled')->sum('paid_amount'));
    }

    public function getNetBalanceAttribute(): float
    {
        $openingBalance = (float) $this->opening_balance;
        $openingEffect = $this->opening_balance_type === 'receivable'
            ? -$openingBalance
            : $openingBalance;

        return $openingEffect + $this->purchases_total - $this->purchases_paid;
    }

    public function getBalanceStatusAttribute(): string
    {
        return match (true) {
            $this->net_balance > 0 => __('We owe supplier'),
            $this->net_balance < 0 => __('Supplier owes us'),
            default => __('Balance Zero'),
        };
    }

    public static function generateSupplierCode(): string
    {
        $nextId = ((int) static::max('id')) + 1;

        do {
            $code = 'SUP-' . str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);
            $nextId++;
        } while (static::where('supplier_code', $code)->exists());

        return $code;
    }
}
