<?php

namespace App\Services;

use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryService
{
    /**
     * Menangani Stok Masuk (Stok Awal / Pembelian)
     * INI ADALAH VERSI YANG SUDAH DI-UPDATE
     */
    public function addStock(
        int $itemId, 
        int $vendorId, 
        int $quantity, 
        float $costPerUnit, 
        string $notes = null, 
        ?string $movement_date = null,  // <-- Ini argumen baru
        ?string $invoice_number = null  // <-- Ini argumen baru
    ) {
        return DB::transaction(function () use ($itemId, $vendorId, $quantity, $costPerUnit, $notes, $movement_date, $invoice_number) {
            
            $item = Item::lockForUpdate()->find($itemId);

            $currentStock = $item->current_stock;
            $currentAvgCost = $item->average_cost;
            $currentTotalValue = $currentStock * $currentAvgCost;

            $newStockValue = $quantity * $costPerUnit;

            $newTotalStock = $currentStock + $quantity;
            $newTotalValue = $currentTotalValue + $newStockValue;

            // HITUNG HARGA RATA-RATA BARU (Weighted Average Cost)
            $newAverageCost = ($newTotalStock > 0) ? $newTotalValue / $newTotalStock : 0;

            // Update master item
            $item->current_stock = $newTotalStock;
            $item->average_cost = $newAverageCost;
            $item->save();

            // Catat di buku besar (ledger)
            StockMovement::create([
                'item_id' => $itemId,
                'vendor_id' => $vendorId,
                'invoice_number' => $invoice_number, // <-- PASTIKAN INI ADA
                'type' => 'in',
                'quantity' => $quantity,
                'cost_per_unit' => $costPerUnit, // Harga beli aktual
                'total_cost' => $newStockValue,
                'movement_date' => $movement_date ? Carbon::parse($movement_date) : Carbon::now(), // <-- PASTIKAN INI DIUBAH
                'notes' => $notes,
            ]);

            return $item;
        });
    }

    /**
     * Menangani Stok Pakai (Stok Keluar)
     */
    public function useStock(int $itemId, int $quantity, string $notes = null)
    {
        return DB::transaction(function () use ($itemId, $quantity, $notes) {
            
            $item = Item::lockForUpdate()->find($itemId);

            if ($item->current_stock < $quantity) {
                throw new \Exception('Stok tidak mencukupi.');
            }

            $currentAvgCost = $item->average_cost;
            $usedStockValue = $quantity * $currentAvgCost;

            $item->current_stock -= $quantity;
            $item->save();

            StockMovement::create([
                'item_id' => $itemId,
                'vendor_id' => null,
                'type' => 'out',
                'quantity' => $quantity,
                'cost_per_unit' => $currentAvgCost,
                'total_cost' => $usedStockValue,
                'movement_date' => Carbon::now(),
                'notes' => $notes,
            ]);

            return $item;
        });
    }

    /**
     * Menangani Penyesuaian Stok (Stock Opname)
     * (Ini adalah fungsi yang dipanggil oleh Approval Controller)
     */
    public function adjustStock(int $itemId, int $newPhysicalStock, string $notes = null)
    {
        return DB::transaction(function () use ($itemId, $newPhysicalStock, $notes) {

            $item = Item::lockForUpdate()->find($itemId);
            $currentStock = $item->current_stock;
            $currentAvgCost = $item->average_cost;

            $adjustmentQuantity = $newPhysicalStock - $currentStock;

            if ($adjustmentQuantity == 0) {
                return $item;
            }

            $type = ($adjustmentQuantity > 0) ? 'in' : 'out';
            $quantityForDb = abs($adjustmentQuantity);
            $totalCostAdjustment = $quantityForDb * $currentAvgCost;

            $item->current_stock = $newPhysicalStock;
            $item->save();

            StockMovement::create([
                'item_id'       => $itemId,
                'vendor_id'     => null,
                'type'          => $type,
                'quantity'      => $quantityForDb,
                'cost_per_unit' => $currentAvgCost,
                'total_cost'    => $totalCostAdjustment,
                'movement_date' => Carbon::now(),
                'notes'         => $notes, // Catatan sudah berisi [ADJUSTMENT] dari controller
            ]);

            return $item;
        });
    }
}