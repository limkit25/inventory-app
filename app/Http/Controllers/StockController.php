<?php

namespace App\Http\Controllers;

// Impor semua yang kita butuhkan
use App\Models\Item;
use App\Models\Vendor;
use App\Models\StockAdjustment; // <-- Untuk membuat pengajuan
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;   // <-- Untuk transaction
use Illuminate\Support\Facades\Auth;  // <-- Untuk ID pengguna

class StockController extends Controller
{
    protected $inventoryService;

    // Inject InventoryService
    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Menampilkan form stok masuk (multi-item)
     */
    public function createStockIn()
    {
        $items = Item::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();
        return view('transactions.stock_in', compact('items', 'vendors'));
    }

    /**
     * Menyimpan batch stok masuk (multi-item)
     */
    public function storeStockIn(Request $request)
    {
        // 1. Validasi data header (Vendor, Tanggal, Invoice)
        $request->validate([
            'vendor_id'     => 'required|exists:vendors,id',
            'movement_date' => 'required|date',
            'invoice_number'=> 'nullable|string|max:255',
            
            // 2. Validasi data detail (array barang)
            'items'         => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost_per_unit' => 'required|numeric|min:0',
        ]);

        // Mulai Database Transaction
        DB::beginTransaction();
        try {
            $vendorId = $request->vendor_id;
            $movementDate = $request->movement_date;
            $invoiceNumber = $request->invoice_number;
            $notes = $request->notes; // Catatan header

            // 3. Looping untuk setiap barang yang di-input
            foreach ($request->items as $itemData) {
                
                $this->inventoryService->addStock(
                    $itemData['item_id'],
                    $vendorId,
                    $itemData['quantity'],
                    $itemData['cost_per_unit'],
                    $notes, // Gunakan catatan header untuk semua item
                    $movementDate,
                    $invoiceNumber
                );
            }

            // 4. Jika semua berhasil, commit
            DB::commit();

        } catch (\Exception $e) {
            // 5. Jika ada satu saja yang gagal, rollback semua
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                         ->withInput(); // Kembalikan input lama
        }

        return redirect()->route('reports.inventory.index')
                         ->with('success', 'Stok berhasil ditambahkan dari invoice ' . $invoiceNumber);
    }

    /**
     * Menampilkan form stok pakai (stok keluar)
     */
    public function createStockOut()
    {
        $items = Item::where('current_stock', '>', 0)->orderBy('name')->get();
        return view('transactions.stock_out', compact('items'));
    }

    /**
     * Menyimpan data stok pakai (stok keluar)
     */
    public function storeStockOut(Request $request)
{
    // 1. Validasi data header (Keperluan, Tanggal)
    $request->validate([
        'notes'         => 'required|string|max:255', // Jadikan wajib
        'movement_date' => 'required|date',

        // 2. Validasi data detail (array barang)
        'items'         => 'required|array|min:1',
        'items.*.item_id' => 'required|exists:items,id',
        'items.*.quantity' => 'required|integer|min:1',
    ]);

    // Mulai Database Transaction
    DB::beginTransaction();
    try {
        $notes = $request->notes;
        $movementDate = $request->movement_date; // Kita belum gunakan ini di service, tapi ambil saja

        // 3. Looping untuk setiap barang yang di-input
        foreach ($request->items as $itemData) {

            // Panggil service asli untuk mengurangi stok
            // Catatan: Service useStock saat ini belum menerima movement_date,
            // jadi tanggalnya akan otomatis hari ini (Carbon::now()).
            // Kita bisa modifikasi service jika perlu.
            $this->inventoryService->useStock(
                $itemData['item_id'],
                $itemData['quantity'],
                $notes // Gunakan catatan header untuk semua item
            );
        }

        // 4. Jika semua berhasil, commit
        DB::commit();

    } catch (\Exception $e) {
        // 5. Jika ada satu saja yang gagal (misal: stok tidak cukup), rollback semua
        DB::rollBack();
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                     ->withInput(); // Kembalikan input lama
    }

    return redirect()->route('reports.inventory.index')
                     ->with('success', 'Stok berhasil dikeluarkan untuk keperluan: ' . $notes);
}

    /**
     * Menampilkan form penyesuaian stok.
     */
    public function createAdjustment()
    {
        $items = Item::orderBy('name')->get();
        return view('transactions.adjustment', compact('items'));
    }

    /**
     * Menyimpan data penyesuaian stok SEBAGAI PENGAJUAN.
     * (Logika ini sudah diubah untuk alur approval)
     */
    public function storeAdjustment(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'new_physical_stock' => 'required|integer|min:0', // Stok baru bisa 0
            'notes' => 'required|string|max:255', // Wajib diisi alasan penyesuaian
        ]);

        $item = Item::findOrFail($request->item_id);
        $stockInSystem = $item->current_stock;
        $stockPhysical = $request->new_physical_stock;

        // Hitung selisihnya
        $quantityDifference = $stockPhysical - $stockInSystem;
        $redirectMessage = ''; // Siapkan variabel pesan

        try {
            if ($quantityDifference == 0) {
            // JIKA TIDAK ADA SELISIH: Tetap buat record, tapi langsung 'approved'
            StockAdjustment::create([
                'item_id'         => $item->id,
                'user_id'         => Auth::id(),
                'status'          => 'approved', // Langsung approved
                'stock_in_system' => $stockInSystem,
                'stock_physical'  => $stockPhysical,
                'quantity'        => 0, // Selisihnya 0
                'notes'           => $request->notes . ' (Stok Cocok)', // Tambahkan keterangan
                'approved_by'     => Auth::id(), // Dianggap diapprove oleh yg cek
                'approved_at'     => now(),
            ]);
            $redirectMessage = 'Hasil Stock Opname cocok dengan sistem dan sudah tercatat.';

        } else {
            // JIKA ADA SELISIH: Buat record PENDING (seperti sebelumnya)
            StockAdjustment::create([
                'item_id'         => $item->id,
                'user_id'         => Auth::id(),
                'status'          => 'pending',
                'stock_in_system' => $stockInSystem,
                'stock_physical'  => $stockPhysical,
                'quantity'        => $quantityDifference,
                'notes'           => $request->notes,
            ]);
            $redirectMessage = 'Pengajuan penyesuaian stok berhasil dikirim dan menunggu persetujuan.';
        }

    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }

    // Redirect dengan pesan yang sesuai
    return redirect()->route('reports.inventory.index') // Atau ke halaman lain
                     ->with('success', $redirectMessage);
    }
}