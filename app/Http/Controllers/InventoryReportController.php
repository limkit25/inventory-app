<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryReportExport;

class InventoryReportController extends Controller
{

    public function index()
    {

        $items = Item::orderBy('name')->get();
        return view('reports.inventory', compact('items'));
    }


    public function showStockCard(Item $item)
    {
        
        $movements = $item->stockMovements()
                          ->orderBy('movement_date', 'desc')
                          ->orderBy('id', 'desc')
                          ->get();

        return view('reports.stock_card', compact('item', 'movements'));
    }
    public function exportExcel() 
{
    return Excel::download(new InventoryReportExport, 'laporan_stok_akhir.xlsx');
}
}