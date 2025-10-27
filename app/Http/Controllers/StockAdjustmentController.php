<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Services\InventoryService; // <-- Panggil InventoryService
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\StockAdjustmentHistoryExport;
use Maatwebsite\Excel\Facades\Excel;


class StockAdjustmentController extends Controller
{
    protected $inventoryService;

    /**
     * Inject InventoryService
     */
    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Menampilkan halaman daftar pengajuan penyesuaian.
     * Method: GET
     * Route: adjustments.index
     */
    public function index()
    {
        // Ambil semua pengajuan yang masih 'pending'
        $adjustments = StockAdjustment::with(['item', 'requestor'])
                                      ->where('status', 'pending')
                                      ->orderBy('created_at', 'desc')
                                      ->get();

        return view('transactions.adjustments_index', compact('adjustments'));
    }

    /**
     * Menyetujui (Approve) pengajuan penyesuaian stok.
     * Method: POST
     * Route: adjustments.approve
     */
    public function approve(StockAdjustment $adjustment)
    {
        // 1. Pastikan statusnya masih pending
        if ($adjustment->status != 'pending') {
            return redirect()->route('adjustments.index')
                             ->with('error', 'Pengajuan ini sudah diproses.');
        }

        try {
            // 2. JALANKAN LOGIKA INTI
            // Panggil InventoryService untuk mengubah stok di tabel 'items'
            $this->inventoryService->adjustStock(
                $adjustment->item_id,
                $adjustment->stock_physical, // Stok fisik baru yang diajukan
                $adjustment->notes . " (Approved by " . Auth::user()->name . ")"
            );

            // 3. Update status pengajuan
            $adjustment->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

        } catch (\Exception $e) {
            return redirect()->route('adjustments.index')
                             ->with('error', 'Gagal menyetujui: ' . $e->getMessage());
        }

        return redirect()->route('adjustments.index')
                         ->with('success', 'Pengajuan stok berhasil disetujui.');
    }

    /**
     * Menolak (Reject) pengajuan penyesuaian stok.
     * Method: POST
     * Route: adjustments.reject
     */
    public function reject(StockAdjustment $adjustment)
    {
        // 1. Pastikan statusnya masih pending
        if ($adjustment->status != 'pending') {
            return redirect()->route('adjustments.index')
                             ->with('error', 'Pengajuan ini sudah diproses.');
        }

        // 2. Update status pengajuan (stok tidak berubah)
        $adjustment->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(), // Tetap catat siapa yang proses
            'approved_at' => now()
        ]);

        return redirect()->route('adjustments.index')
                         ->with('success', 'Pengajuan stok berhasil ditolak.');
    }
    public function history(Request $request) // <-- Tambahkan Request $request
{
    // 1. Ambil input tanggal
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // 2. Buat query dasar
    $query = StockAdjustment::with(['item', 'requestor', 'approver'])
                            ->orderBy('created_at', 'desc'); // Tanggal pengajuan

    // 3. Tambahkan filter HANYA JIKA kedua tanggal diisi
    if ($startDate && $endDate) {
        // Filter berdasarkan tanggal PENGAJUAN (created_at)
        $query->whereBetween('created_at', [
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59'
        ]);
    }

    // 4. Eksekusi query
    $adjustments = $query->get();

    // 5. Kirim data (termasuk nilai filter) ke view
    return view('reports.adjustment_history', compact(
        'adjustments', 
        'startDate', 
        'endDate'
    ));
}   
public function exportHistoryExcel()
{
    return Excel::download(new StockAdjustmentHistoryExport, 'riwayat_stock_opname.xlsx');
}
}