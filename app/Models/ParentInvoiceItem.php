<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentInvoiceItem extends Model
{
    protected $fillable = [
        'parent_invoice_id',
        'student_id',
        'student_class_id',
        'class_id',
        'fee_type_id',
        'fee_group_assignment_id',
        'fee_discount_id',
        'inventory_sale_id',
        'billing_month',
        'billing_year',
        'description',
        'gross_amount',
        'discount_amount',
        'amount',
        'is_previous_balance',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'amount' => 'decimal:2',
        'is_previous_balance' => 'boolean',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ParentInvoice::class, 'parent_invoice_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function studentClass(): BelongsTo
    {
        return $this->belongsTo(StudentClass::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function feeType(): BelongsTo
    {
        return $this->belongsTo(FeeType::class);
    }

    public function feeGroupAssignment(): BelongsTo
    {
        return $this->belongsTo(FeeGroupAssignment::class);
    }

    public function feeDiscount(): BelongsTo
    {
        return $this->belongsTo(FeeDiscount::class);
    }

    public function inventorySale(): BelongsTo
    {
        return $this->belongsTo(InventorySale::class);
    }
}
