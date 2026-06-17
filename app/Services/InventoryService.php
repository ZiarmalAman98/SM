<?php

namespace App\Services;

use App\Models\InventoryProduct;
use App\Models\InventoryPurchase;
use App\Models\InventorySale;
use App\Models\InventoryStockMovement;
use App\Models\ParentGuardian;
use App\Models\ParentInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Morilog\Jalali\Jalalian;

class InventoryService
{
    public function syncPurchase(InventoryPurchase $purchase): void
    {
        DB::transaction(function () use ($purchase): void {
            $purchase->load('items');

            $subtotal = (float) $purchase->items->sum('total_amount');
            $discountPercent = min(100, max(0, (float) $purchase->discount_percent));
            $discountAmount = round($subtotal * ($discountPercent / 100), 2);
            $total = max(0, $subtotal - $discountAmount);
            $paid = max(0, (float) $purchase->paid_amount);
            $balance = max(0, $total - $paid);

            $purchase->forceFill([
                'subtotal' => $subtotal,
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'total_amount' => $total,
                'paid_amount' => $paid,
                'balance' => $balance,
                'status' => match (true) {
                    $purchase->status === 'cancelled' => 'cancelled',
                    $paid <= 0 => 'unpaid',
                    $balance <= 0 => 'paid',
                    default => 'partial',
                },
            ])->saveQuietly();

            $purchase->stockMovements()->delete();

            if ($purchase->status === 'cancelled') {
                return;
            }

            foreach ($purchase->items as $item) {
                InventoryStockMovement::create([
                    'inventory_product_id' => $item->inventory_product_id,
                    'type' => 'purchase_in',
                    'quantity' => $item->quantity,
                    'reference_type' => InventoryPurchase::class,
                    'reference_id' => $purchase->id,
                    'movement_date' => $purchase->purchase_date,
                    'notes' => "Purchase {$purchase->purchase_no}",
                ]);
            }
        });
    }

    public function syncSale(InventorySale $sale): void
    {
        DB::transaction(function () use ($sale): void {
            $sale->load(['items', 'payments']);

            if ($sale->status !== 'cancelled') {
                $this->ensureSaleStockAvailable($sale);
            }

            $subtotal = (float) $sale->items->sum('total_amount');
            $total = max(0, $subtotal - (float) $sale->discount_amount);
            $paid = (float) $sale->payments->sum('amount');
            $balance = max(0, $total - $paid);

            $sale->forceFill([
                'subtotal' => $subtotal,
                'total_amount' => $total,
                'paid_amount' => $paid,
                'balance' => $balance,
                'status' => match (true) {
                    $sale->status === 'cancelled' => 'cancelled',
                    $paid <= 0 => 'unpaid',
                    $balance <= 0 => 'paid',
                    default => 'partial',
                },
            ])->saveQuietly();

            $sale->stockMovements()->delete();

            if ($sale->status === 'cancelled') {
                return;
            }

            foreach ($sale->items as $item) {
                InventoryStockMovement::create([
                    'inventory_product_id' => $item->inventory_product_id,
                    'type' => 'sale_out',
                    'quantity' => $item->quantity,
                    'reference_type' => InventorySale::class,
                    'reference_id' => $sale->id,
                    'movement_date' => $sale->sale_date,
                    'notes' => "Sale {$sale->sale_no}",
                ]);
            }

            if ($sale->payment_destination === 'parent_invoice') {
                $this->attachSaleToParentInvoice($sale->fresh(['items.product']));
            } else {
                $this->detachSaleFromParentInvoice($sale);
            }
        });
    }

    public function attachSaleToParentInvoice(InventorySale $sale): ParentInvoice
    {
        if (! $sale->parent_guardian_id) {
            throw ValidationException::withMessages([
                'parent_guardian_id' => __('Parent / Family is required when adding a sale to parent invoice.'),
            ]);
        }

        $period = $this->saleBillingPeriod($sale);
        $invoice = ParentInvoice::query()
            ->where('parent_guardian_id', $sale->parent_guardian_id)
            ->where('billing_month', $period['month'])
            ->where('billing_year', $period['year'])
            ->first();

        if (! $invoice) {
            try {
                $invoice = app(ParentInvoiceBuilder::class)->create([
                    'parent_guardian_id' => $sale->parent_guardian_id,
                    'billing_month' => $period['month'],
                    'billing_year' => $period['year'],
                    'invoice_date' => $sale->sale_date,
                    'due_date' => $sale->sale_date?->copy()?->addDays(7),
                    'notes' => __('Created from inventory sale :sale_no', ['sale_no' => $sale->sale_no]),
                ]);
            } catch (ValidationException) {
                $invoice = $this->createInventoryOnlyParentInvoice($sale, $period);
            }
        }

        $description = __('Inventory Sale :sale_no', ['sale_no' => $sale->sale_no]);
        $itemSummary = $sale->items
            ->map(fn($item) => ($item->product?->name ?? __('Product')) . ' x ' . number_format((float) $item->quantity, 2))
            ->implode(', ');

        $invoice->items()->updateOrCreate(
            ['inventory_sale_id' => $sale->id],
            [
                'student_id' => $sale->student_id,
                'student_class_id' => null,
                'class_id' => null,
                'fee_type_id' => null,
                'fee_group_assignment_id' => null,
                'fee_discount_id' => null,
                'billing_month' => $invoice->billing_month,
                'billing_year' => $invoice->billing_year,
                'description' => trim("{$description}: {$itemSummary}"),
                'gross_amount' => $sale->total_amount,
                'discount_amount' => 0,
                'amount' => $sale->total_amount,
                'is_previous_balance' => false,
            ],
        );

        $this->recalculateParentInvoiceTotals($invoice->fresh(['items', 'payments']));

        $sale->forceFill(['parent_invoice_id' => $invoice->id])->saveQuietly();

        return $invoice;
    }

    private function ensureSaleStockAvailable(InventorySale $sale): void
    {
        foreach ($sale->items->groupBy('inventory_product_id') as $productId => $items) {
            $product = InventoryProduct::find($productId);
            $currentStock = (float) ($product?->current_stock ?? 0);
            $existingSaleQuantity = (float) $sale->stockMovements()
                ->where('inventory_product_id', $productId)
                ->where('type', 'sale_out')
                ->sum('quantity');
            $requestedQuantity = (float) $items->sum('quantity');

            if ($currentStock + $existingSaleQuantity < $requestedQuantity) {
                throw ValidationException::withMessages([
                    'items' => __('Not enough stock for :product. Available: :stock', [
                        'product' => $product?->name ?? __('selected product'),
                        'stock' => number_format($currentStock + $existingSaleQuantity, 2),
                    ]),
                ]);
            }
        }
    }

    private function saleBillingPeriod(InventorySale $sale): array
    {
        $date = $sale->sale_date ? Jalalian::fromDateTime($sale->sale_date) : Jalalian::now();
        $months = array_keys(ParentInvoiceBuilder::MONTHS);

        return [
            'month' => $months[$date->getMonth() - 1] ?? ParentInvoiceBuilder::currentBillingMonth(),
            'year' => $date->getYear(),
        ];
    }

    private function recalculateParentInvoiceTotals(ParentInvoice $invoice): void
    {
        $previousBalance = (float) $invoice->items
            ->where('is_previous_balance', true)
            ->sum('amount');
        $subtotal = (float) $invoice->items
            ->where('is_previous_balance', false)
            ->sum('amount');
        $total = $previousBalance + $subtotal;
        $paid = (float) $invoice->payments->sum('amount');
        $balance = max(0, $total - $paid);

        $invoice->forceFill([
            'previous_balance' => $previousBalance,
            'subtotal' => $subtotal,
            'total_amount' => $total,
            'paid_amount' => $paid,
            'balance' => $balance,
            'status' => match (true) {
                $paid <= 0 => 'issued',
                $balance <= 0 => 'paid',
                default => 'partial',
            },
        ])->saveQuietly();
    }

    private function createInventoryOnlyParentInvoice(InventorySale $sale, array $period): ParentInvoice
    {
        $parentGuardian = ParentGuardian::findOrFail($sale->parent_guardian_id);

        return ParentInvoice::create([
            'parent_guardian_id' => $parentGuardian->id,
            'family_code' => $parentGuardian->family_code,
            'billing_month' => $period['month'],
            'billing_year' => $period['year'],
            'invoice_date' => $sale->sale_date,
            'due_date' => $sale->sale_date?->copy()?->addDays(7),
            'previous_balance' => 0,
            'subtotal' => 0,
            'total_amount' => 0,
            'paid_amount' => 0,
            'balance' => 0,
            'status' => 'issued',
            'notes' => __('Created from inventory sale :sale_no', ['sale_no' => $sale->sale_no]),
        ]);
    }

    private function detachSaleFromParentInvoice(InventorySale $sale): void
    {
        if (! $sale->parent_invoice_id) {
            return;
        }

        $invoice = ParentInvoice::with(['items', 'payments'])->find($sale->parent_invoice_id);

        if (! $invoice) {
            $sale->forceFill(['parent_invoice_id' => null])->saveQuietly();
            return;
        }

        $invoice->items()
            ->where('inventory_sale_id', $sale->id)
            ->delete();

        $this->recalculateParentInvoiceTotals($invoice->fresh(['items', 'payments']));

        $sale->forceFill(['parent_invoice_id' => null])->saveQuietly();
    }
}
