<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Vendor;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class StockController extends Controller {
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService) {
        $this->inventoryService = $inventoryService;
    }

    public function createStockIn() {
        $items = Item::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();
        return view('transactions.stock_in', compact('items', 'vendors'));
    }

    public function storeStockIn(Request $request) {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'vendor_id' => 'required|exists:vendors,id',
            'quantity' => 'required|integer|min:1',
            'cost_per_unit' => 'required|numeric|min:0',
        ]);
        try {
            $this->inventoryService->addStock(
                $request->item_id, $request->vendor_id, $request->quantity,
                $request->cost_per_unit, $request->notes
            );
        } catch (\Exception $e) { return back()->with('error', $e->getMessage()); }
        return redirect()->route('reports.inventory.index')->with('success', 'Stok berhasil ditambahkan.');
    }

    public function createStockOut() {
        $items = Item::where('current_stock', '>', 0)->orderBy('name')->get();
        return view('transactions.stock_out', compact('items'));
    }

    public function storeStockOut(Request $request) {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
        ]);
        try {
            $this->inventoryService->useStock(
                $request->item_id, $request->quantity, $request->notes
            );
        } catch (\Exception $e) { return back()->with('error', $e->getMessage()); }
        return redirect()->route('reports.inventory.index')->with('success', 'Stok berhasil dipakai.');
    }
    public function createAdjustment()
    {
        $items = Item::orderBy('name')->get();
        return view('transactions.adjustment', compact('items'));
    }

    /**
     * Menyimpan data penyesuaian stok.
     * Method: POST
     * Route: stock.adjustment.store
     */
    public function storeAdjustment(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'new_physical_stock' => 'required|integer|min:0', // Stok baru bisa 0
            'notes' => 'required|string|max:255', // Wajib diisi alasan penyesuaian
        ]);

        try {
            $this->inventoryService->adjustStock(
                $request->item_id,
                $request->new_physical_stock,
                $request->notes
            );
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('reports.inventory.index')
                         ->with('success', 'Penyesuaian stok berhasil disimpan.');
    }
}
