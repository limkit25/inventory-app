<?php

namespace App\Exports;

use App\Models\StockAdjustment; // <-- Ganti modelnya
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockAdjustmentHistoryExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Database\Query\Builder
    */
    public function query()
    {
        // Ambil data beserta relasi yang dibutuhkan
        return StockAdjustment::query()
            ->with(['item', 'requestor', 'approver'])
            ->orderBy('created_at', 'desc'); // Urutkan sesuai tampilan
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        // Sesuaikan header dengan tampilan di web
        return [
            'Tgl Diajukan',
            'Diajukan Oleh',
            'Nama Barang',
            'Stok Sistem',
            'Stok Fisik',
            'Selisih',
            'Alasan',
            'Status',
            'Diproses Oleh',
            'Tgl Diproses',
        ];
    }

    /**
    * @param mixed $adjustment
    *
    * @return array
    */
    public function map($adjustment): array
    {
        // Format data sesuai header
        return [
            $adjustment->created_at->format('Y-m-d H:i:s'),
            $adjustment->requestor->name ?? '-',
            $adjustment->item->name ?? 'N/A',
            $adjustment->stock_in_system,
            $adjustment->stock_physical,
            $adjustment->quantity, // Ekspor selisih sebagai angka (+/-)
            $adjustment->notes,
            ucfirst($adjustment->status), // Tampilkan status (Pending, Approved, Rejected)
            $adjustment->approver->name ?? '-',
            $adjustment->approved_at ? $adjustment->approved_at->format('Y-m-d H:i:s') : '-',
        ];
    }

    /**
     * Terapkan style (opsional, misal bold header)
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style baris pertama (header) agar bold.
            1 => ['font' => ['bold' => true]],
        ];
    }
}