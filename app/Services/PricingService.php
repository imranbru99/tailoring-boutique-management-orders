<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\GarmentType;
use App\Models\Order;

class PricingService
{
    /**
     * Calculate suggested retail price and expected margin for a custom garment
     *
     * @return array{
     *     material_cost: float,
     *     labor_cost: float,
     *     additional_services: float,
     *     subtotal: float,
     *     expected_margin: float,
     *     margin_percentage: float
     * }
     */
    public function estimateGarmentPrice(
        GarmentType $garmentType,
        float $materialCost = 0.0,
        float $laborCost = 0.0,
        float $additionalServices = 0.0,
        float $targetMarginPercent = 40.0
    ): array {
        $baseLabor = $laborCost > 0 ? $laborCost : (float) $garmentType->base_price;
        $totalCost = $materialCost + $baseLabor + $additionalServices;

        $sellingPrice = $targetMarginPercent > 0 && $targetMarginPercent < 100
            ? round($totalCost / (1 - ($targetMarginPercent / 100)), 2)
            : $totalCost * 1.5;

        $margin = $sellingPrice - $totalCost;
        $marginPercent = $sellingPrice > 0 ? round(($margin / $sellingPrice) * 100, 2) : 0.0;

        return [
            'material_cost' => round($materialCost, 2),
            'labor_cost' => round($baseLabor, 2),
            'additional_services' => round($additionalServices, 2),
            'subtotal' => round($sellingPrice, 2),
            'expected_margin' => round($margin, 2),
            'margin_percentage' => $marginPercent,
        ];
    }

    /**
     * Calculate item price breakdown
     */
    public function calculateItemPrice(
        float $materialCost,
        float $laborCost,
        float $additionalCost = 0.0,
        float $markupPercentage = 50.0,
        float $taxRate = 0.0,
        float $discountAmount = 0.0
    ): array {
        $baseCost = $materialCost + $laborCost + $additionalCost;
        $subtotalBeforeDiscount = round($baseCost * (1 + ($markupPercentage / 100)), 2);
        $subtotal = round(max(0, $subtotalBeforeDiscount - $discountAmount), 2);
        $taxAmount = round($subtotal * ($taxRate / 100), 2);
        $totalPrice = round($subtotal + $taxAmount, 2);
        $estimatedProfit = round($subtotal - $baseCost, 2);

        return [
            'base_cost' => $baseCost,
            'subtotal_before_discount' => $subtotalBeforeDiscount,
            'discount_amount' => $discountAmount,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_price' => $totalPrice,
            'estimated_profit' => $estimatedProfit,
        ];
    }

    /**
     * Compute full order breakdown with coupon code and branch tax
     */
    public function calculateOrderTotals(
        Order $order,
        ?string $couponCode = null,
        float $taxRate = 0.0
    ): array {
        $materialCost = (float) $order->items()->sum('material_cost');
        $laborCost = (float) $order->items()->sum('labor_cost');
        $itemsTotal = (float) $order->items()->sum('total_price');

        $subtotal = $itemsTotal > 0 ? $itemsTotal : ($materialCost + $laborCost);
        $additional = (float) $order->additional_charges;

        $discount = (float) $order->discount_amount;
        if ($couponCode) {
            $coupon = Coupon::where('tenant_id', $order->tenant_id)
                ->where('code', $couponCode)
                ->first();

            if ($coupon && $coupon->isValid($subtotal + $additional)) {
                $discount = $coupon->calculateDiscount($subtotal + $additional);
            }
        }

        $taxableAmount = max(0, $subtotal + $additional - $discount);
        $taxAmount = round($taxableAmount * ($taxRate / 100), 2);
        $grandTotal = round($taxableAmount + $taxAmount, 2);

        $paidAmount = (float) $order->payments()->where('is_refund', false)->sum('amount');
        $refundedAmount = (float) $order->payments()->where('is_refund', true)->sum('amount');
        $netPaid = max(0, $paidAmount - $refundedAmount);
        $balance = max(0, $grandTotal - $netPaid);

        return [
            'material_cost' => $materialCost,
            'labor_cost' => $laborCost,
            'subtotal' => $subtotal,
            'additional_charges' => $additional,
            'discount_amount' => $discount,
            'taxable_amount' => $taxableAmount,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'grand_total' => $grandTotal,
            'net_paid' => $netPaid,
            'balance_due' => $balance,
        ];
    }
}
