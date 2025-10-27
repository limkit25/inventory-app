<?php

use Illuminate\Support\Facades\Route;

// ==========================================================
// PASTIKAN SEMUA CONTROLLER INI ADA DI ATAS
// ==========================================================
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\CategoryController;

// Model dan Facade untuk Rute Dashboard
use App\Models\Item;
use App\Models\Vendor;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
// ==========================================================


// Rute Halaman Awal
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login'); 
});

// Rute Autentikasi (Login, Register, dll.)
require __DIR__.'/auth.php';


// === GRUP RUTE APLIKASI UTAMA (WAJIB LOGIN) ===
Route::middleware('auth')->group(function () {

    // == RUTE PUBLIK (Semua yang login bisa akses) ==
    Route::get('/dashboard', function () {
        $totalItems = Item::count();
        $totalVendors = Vendor::count();
        $totalValue = Item::select(DB::raw('SUM(current_stock * average_cost) as total'))
                          ->first()
                          ->total ?? 0;
        $lowStockItems = Item::where('current_stock', '<', 10)
                             ->orderBy('current_stock', 'asc')
                             ->get();
        $recentMovements = StockMovement::with(['item', 'vendor'])
                                        ->orderBy('movement_date', 'desc')
                                        ->limit(5)
                                        ->get();
        return view('dashboard', compact(
            'totalItems', 'totalVendors', 'totalValue', 'lowStockItems', 'recentMovements'
        ));
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // == RUTE MANAJEMEN PENGGUNA (Hanya Admin) ==
    Route::resource('users', UserController::class)->middleware('permission:manage-users');


    // == RUTE MASTER DATA (Hanya yang punya izin 'manage-master-data') ==
    Route::middleware('permission:manage-master-data')->group(function () {
        Route::resource('items', ItemController::class);
        Route::resource('vendors', VendorController::class);
        Route::resource('categories', CategoryController::class);
    });


    // == RUTE TRANSAKSI (Hanya yang punya izin 'perform-transactions') ==
    Route::middleware('permission:perform-transactions')->group(function () {
        // Ini adalah rute untuk form multi-item kita
        Route::get('stock/in', [StockController::class, 'createStockIn'])->name('stock.in.create');
        Route::post('stock/in', [StockController::class, 'storeStockIn'])->name('stock.in.store');
        
        Route::get('stock/out', [StockController::class, 'createStockOut'])->name('stock.out.create');
        Route::post('stock/out', [StockController::class, 'storeStockOut'])->name('stock.out.store');
        
        // Ini rute untuk MENGAJUKAN penyesuaian
        Route::get('stock/adjustment', [StockController::class, 'createAdjustment'])->name('stock.adjustment.create');
        Route::post('stock/adjustment', [StockController::class, 'storeAdjustment'])->name('stock.adjustment.store');
    });


    // == RUTE PERSETUJUAN (Hanya Admin/Manajer) ==
    Route::middleware('permission:approve-adjustments')->group(function () {
        // Halaman utama daftar persetujuan
        Route::get('adjustments', [StockAdjustmentController::class, 'index'])->name('adjustments.index');
        // Rute untuk tombol Approve
        Route::post('adjustments/{adjustment}/approve', [StockAdjustmentController::class, 'approve'])->name('adjustments.approve');
        // Rute untuk tombol Reject
        Route::post('adjustments/{adjustment}/reject', [StockAdjustmentController::class, 'reject'])->name('adjustments.reject');
    });


    // == RUTE LAPORAN (Hanya yang punya izin 'view-reports') ==
    Route::middleware('permission:view-reports')->group(function () {
        Route::get('reports/inventory', [InventoryReportController::class, 'index'])->name('reports.inventory.index');
        Route::get('reports/stockcard/{item}', [InventoryReportController::class, 'showStockCard'])->name('reports.stockcard.show');
        Route::get('reports/inventory/export', [InventoryReportController::class, 'exportExcel'])->name('reports.inventory.export');
        Route::get('reports/adjustments', [StockAdjustmentController::class, 'history'])->name('adjustments.history');
        Route::get('reports/adjustments/export', [StockAdjustmentController::class, 'exportHistoryExcel'])->name('adjustments.history.export');
    });

});