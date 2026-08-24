<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class EmployeeAllocation extends Model
{
    use HasFactory;

    protected $casts = [
        'allocation_date' => 'date',
        'return_date' => 'date',
        'quantity' => 'integer',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    protected static function booted(): void
    {
        static::creating(function (EmployeeAllocation $allocation): void {
            if (blank($allocation->return_date)) {
                $allocation->applyStockChange('issue', (int) $allocation->quantity, (int) $allocation->material_id);
            }
        });

        static::updating(function (EmployeeAllocation $allocation): void {
            $originalMaterialId = (int) $allocation->getOriginal('material_id');
            $originalQuantity = (int) $allocation->getOriginal('quantity');
            $wasReturned = filled($allocation->getOriginal('return_date'));
            $isReturned = filled($allocation->return_date);
            $newMaterialId = (int) $allocation->material_id;
            $newQuantity = (int) $allocation->quantity;

            if (! $wasReturned && $isReturned) {
                $allocation->applyStockChange('return', $originalQuantity, $originalMaterialId);

                return;
            }

            if ($wasReturned && ! $isReturned) {
                $allocation->applyStockChange('issue', $newQuantity, $newMaterialId);

                return;
            }

            if ($wasReturned && $isReturned) {
                return;
            }

            if ($originalMaterialId !== $newMaterialId) {
                $allocation->applyStockChange('return', $originalQuantity, $originalMaterialId);
                $allocation->applyStockChange('issue', $newQuantity, $newMaterialId);

                return;
            }

            $difference = $newQuantity - $originalQuantity;

            if ($difference > 0) {
                $allocation->applyStockChange('issue', $difference, $newMaterialId);
            } elseif ($difference < 0) {
                $allocation->applyStockChange('return', abs($difference), $newMaterialId);
            }
        });

        static::deleting(function (EmployeeAllocation $allocation): void {
            if (blank($allocation->return_date)) {
                $allocation->applyStockChange('return', (int) $allocation->quantity, (int) $allocation->material_id);
            }
        });
    }

    private function applyStockChange(string $type, int $quantity, int $materialId): void
    {
        if ($quantity <= 0 || $materialId <= 0) {
            return;
        }

        $material = Material::query()->find($materialId);

        if (! $material) {
            throw ValidationException::withMessages([
                'material_id' => __('Material not found'),
            ]);
        }

        $material->updateStock($type, $quantity);
    }
}
