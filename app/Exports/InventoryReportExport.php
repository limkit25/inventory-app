<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InventoryReportExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
    * @return \Illuminate\Database\Query\Builder
    */
    public function query()
    {
        // 1. Ambil data yang ingin diekspor
        return Item::query()->orderBy('name');
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        // 2. Tentukan judul kolom di file Excel
        return [
            'ID',
            'Nama Barang',
            'SKU',
            'Satuan',
            'Stok Akhir',
            'Harga Rata-rata (HPP)',
            'Total Nilai Persediaan',
        ];
    }

    /**
    * @param mixed $item
    *
    * @return array
    */
    public function map($item): array
    {
        // 3. Tentukan data di setiap baris dan formatnya
        return [
            $item->id,
            $item->name,
            $item->sku,
            $item->unit,
            $item->current_stock, // Ekspor sebagai angka
            $item->average_cost,  // Ekspor sebagai angka
            $item->current_stock * $item->average_cost // Ekspor sebagai angka
        ];
    }
}