<?php

namespace App\Services;

use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryService
{
    public function addStock(int $itemId, int $vendorId, int $quantity, float $costPerUnit, string $notes = null)
    {
        return DB::transaction(function () use ($itemId, $vendorId, $quantity, $costPerUnit, $notes) {

            $item = Item::lockForUpdate()->find($itemId);

            $currentStock = $item->current_stock;
            $currentAvgCost = $item->average_cost;
            $currentTotalValue = $currentStock * $currentAvgCost;

            $newStockValue = $quantity * $costPerUnit;

            $newTotalStock = $currentStock + $quantity;
            $newTotalValue = $currentTotalValue + $newStockValue;

            // Ini adalah LOGIKA RATA-RATA TERTIMBANG (WAC)
            $newAverageCost = ($newTotalStock > 0) ? $newTotalValue / $newTotalStock : 0;

            // Update master item
            $item->current_stock = $newTotalStock;
            $item->average_cost = $newAverageCost;
            $item->save();

            // Catat di ledger
            StockMovement::create([
                'item_id' => $itemId,
                'vendor_id' => $vendorId,
                'type' => 'in',
                'quantity' => $quantity,
                'cost_per_unit' => $costPerUnit, // Harga beli aktual
                'total_cost' => $newStockValue,
                'movement_date' => Carbon::now(),
                'notes' => $notes,
            ]);

            return $item;
        });
    }

    public function useStock(int $itemId, int $quantity, string $notes = null)
    {
        return DB::transaction(function () use ($itemId, $quantity, $notes) {

            $item = Item::lockForUpdate()->find($itemId);

            if ($item->current_stock < $quantity) {
                throw new \Exception('Stok tidak mencukupi.');
            }

            $currentAvgCost = $item->average_cost;
            $usedStockValue = $quantity * $currentAvgCost;

            // Update master item
            $item->current_stock -= $quantity;
            // average_cost TIDAK BERUBAH saat stok keluar
            $item->save();

            // Catat di ledger
            StockMovement::create([
                'item_id' => $itemId,
                'vendor_id' => null,
                'type' => 'out',
                'quantity' => $quantity,
                'cost_per_unit' => $currentAvgCost, // Biaya keluar = harga rata-rata
                'total_cost' => $usedStockValue,
                'movement_date' => Carbon::now(),
                'notes' => $notes,
            ]);

            return $item;
        });
    }
    /**
     * Menangani Penyesuaian Stok (Stock Opname)
     *
     * @param integer $itemId
     * @param integer $newPhysicalStock Stok fisik baru yang dihitung
     * @param string|null $notes
     * @return Item
     */
    public function adjustStock(int $itemId, int $newPhysicalStock, string $notes = null)
    {
        return DB::transaction(function () use ($itemId, $newPhysicalStock, $notes) {

            $item = Item::lockForUpdate()->find($itemId);

            $currentStock = $item->current_stock;
            $currentAvgCost = $item->average_cost;

            // Hitung selisihnya
            $adjustmentQuantity = $newPhysicalStock - $currentStock;

            // Jika tidak ada perubahan, tidak perlu lakukan apa-apa
            if ($adjustmentQuantity == 0) {
                return $item;
            }

            // Tentukan tipenya (masuk atau keluar)
            $type = ($adjustmentQuantity > 0) ? 'in' : 'out';
            $quantityForDb = abs($adjustmentQuantity);
            $totalCostAdjustment = $quantityForDb * $currentAvgCost;

            // 1. Update master item
            $item->current_stock = $newPhysicalStock;
            // Harga rata-rata (average_cost) TIDAK BERUBAH saat penyesuaian
            $item->save();

            // 2. Catat di buku besar (ledger)
            StockMovement::create([
                'item_id'       => $itemId,
                'vendor_id'     => null, // Penyesuaian tidak terkait vendor
                'type'          => $type,
                'quantity'      => $quantityForDb,
                'cost_per_unit' => $currentAvgCost, // Penyesuaian dinilai sebesar HPP
                'total_cost'    => $totalCostAdjustment,
                'movement_date' => Carbon::now(),
                'notes'         => '[ADJUSTMENT] ' . ($notes ?? 'Penyesuaian stok'),
            ]);

            return $item;
        });
    }
}