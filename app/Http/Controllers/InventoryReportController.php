<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request; // <-- TAMBAHKAN INI
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryReportExport;

class InventoryReportController extends Controller
{
    /**
     * Menampilkan Laporan Stok Akhir (halaman utama laporan)
     */
    public function index()
    {
        $items = Item::orderBy('name')->get();
        return view('reports.inventory', compact('items'));
    }

    /**
     * ===================================================================
     * FUNGSI INI KITA MODIFIKASI
     * ===================================================================
     * Menampilkan Laporan Kartu Stok untuk SATU item
     */
    public function showStockCard(Request $request, Item $item) // <-- TAMBAHKAN Request $request
    {
        // 1. Ambil input tanggal dari request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // 2. Buat query dasar
        $query = $item->stockMovements()
                      ->with(['vendor']) // (relasi vendor sudah kita tambahkan sebelumnya)
                      ->orderBy('movement_date', 'desc')
                      ->orderBy('id', 'desc');

        // 3. Tambahkan filter HANYA JIKA kedua tanggal diisi
        if ($startDate && $endDate) {
            // Pastikan formatnya benar untuk perbandingan datetime
            $query->whereBetween('movement_date', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
        }

        // 4. Eksekusi query
        $movements = $query->get();

        // 5. Kirim data (termasuk nilai filter) ke view
        return view('reports.stock_card', compact(
            'item', 
            'movements', 
            'startDate', 
            'endDate'
        ));
    }

    /**
     * Handle ekspor Laporan Stok Akhir ke Excel.
     */
    public function exportExcel() 
    {
        return Excel::download(new InventoryReportExport, 'laporan_stok_akhir.xlsx');
    }
}