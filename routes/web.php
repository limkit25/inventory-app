<?php

use Illuminate\Support\Facades\Route;

// Import semua Controller yang kita butuhkan
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\VendorController;
use App\Models\Item;
use App\Models\Vendor;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sinilah Anda dapat mendaftarkan rute web untuk aplikasi Anda.
| Rute-rute ini dimuat oleh RouteServiceProvider dan semuanya akan
| ditugaskan ke grup middleware "web".
|
*/

// Rute Halaman Awal (Welcome)
// Ini adalah halaman publik sebelum login
Route::get('/', function () {
    // Jika sudah login, arahkan ke dashboard
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    // Jika belum, tampilkan halaman login
    return redirect()->route('login'); 
});


// Rute Autentikasi (Login, Register, dll.)
// File ini dibuat otomatis oleh Laravel Breeze
require __DIR__.'/auth.php';


// === GRUP RUTE APLIKASI UTAMA ===
// Semua rute di bawah ini memerlukan pengguna untuk login
// Mereka juga otomatis menggunakan layout AdminLTE
// -----------------------------------------------------------------

Route::middleware('auth')->group(function () {

    // Rute Dashboard
    Route::get('/dashboard', function () {

        // 1. Ambil data untuk InfoBox
        $totalItems = Item::count();
        $totalVendors = Vendor::count();

        // Menghitung total nilai persediaan (SUM(stok * harga_rata2))
        $totalValue = Item::select(DB::raw('SUM(current_stock * average_cost) as total'))
                          ->first()
                          ->total ?? 0;

        // 2. Ambil data untuk Tabel Stok Menipis (misal, di bawah 10)
        $lowStockItems = Item::where('current_stock', '<', 10)
                             ->orderBy('current_stock', 'asc')
                             ->get();

        // 3. Ambil data untuk Transaksi Terakhir
        $recentMovements = StockMovement::with(['item', 'vendor']) // Ambil relasinya
                                        ->orderBy('movement_date', 'desc')
                                        ->limit(5)
                                        ->get();

        // 4. Kirim semua data ke view
        return view('dashboard', compact(
            'totalItems',
            'totalVendors',
            'totalValue',
            'lowStockItems',
            'recentMovements'
        ));

    })->name('dashboard');

    // Rute Profil Bawaan Breeze
    // (Biar pengguna bisa ganti password)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('users', UserController::class)->middleware('permission:manage-users');


    Route::middleware('permission:manage-master-data')->group(function () {
        Route::resource('items', ItemController::class);
        Route::resource('vendors', VendorController::class);
    });


    // == RUTE TRANSAKSI (Hanya yang punya izin 'perform-transactions') ==
    Route::middleware('permission:perform-transactions')->group(function () {
        Route::get('stock/in', [StockController::class, 'createStockIn'])->name('stock.in.create');
        Route::post('stock/in', [StockController::class, 'storeStockIn'])->name('stock.in.store');
        
        Route::get('stock/out', [StockController::class, 'createStockOut'])->name('stock.out.create');
        Route::post('stock/out', [StockController::class, 'storeStockOut'])->name('stock.out.store');
        
        Route::get('stock/adjustment', [StockController::class, 'createAdjustment'])->name('stock.adjustment.create');
        Route::post('stock/adjustment', [StockController::class, 'storeAdjustment'])->name('stock.adjustment.store');
    });


    // == RUTE LAPORAN (Hanya yang punya izin 'view-reports') ==
    Route::middleware('permission:view-reports')->group(function () {
        Route::get('reports/inventory', [InventoryReportController::class, 'index'])->name('reports.inventory.index');
        Route::get('reports/stockcard/{item}', [InventoryReportController::class, 'showStockCard'])->name('reports.stockcard.show');
        Route::get('reports/inventory/export', [InventoryReportController::class, 'exportExcel'])->name('reports.inventory.export');
    });

});