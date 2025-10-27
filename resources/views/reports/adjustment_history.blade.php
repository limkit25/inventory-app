@extends('adminlte::page')

@section('title', 'Riwayat Stock Opname')

@section('content_header')
    <h1>Riwayat Stock Opname (Penyesuaian Stok)</h1>
@stop

@section('content')
    <x-adminlte-card theme="primary" theme-mode="outline" icon="fas fa-history" title="Semua Catatan Penyesuaian Stok">
        <form method="GET" action="{{ route('adjustments.history') }}" class="mb-4">
        <div class="row">
            <div class="col-md-5">
                <x-adminlte-input name="start_date" label="Tanggal Mulai (Pengajuan)" type="date"
                    value="{{ $startDate ?? '' }}" />
            </div>
            <div class="col-md-5">
                <x-adminlte-input name="end_date" label="Tanggal Selesai (Pengajuan)" type="date"
                    value="{{ $endDate ?? '' }}" />
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('adjustments.history') }}" class="btn btn-default" title="Reset Filter">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>
    <hr>
        <a href="{{ route('adjustments.history.export') }}" class="btn btn-success mb-3">
        <i class="fas fa-file-excel"></i> Ekspor ke Excel
    </a>
        @php
        $heads = [
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

        $config = [
            'data' => [],
            'order' => [[0, 'desc']], // Terbaru di atas
            'columns' => [
                null, null, null,
                ['className' => 'text-center'],
                ['className' => 'text-center'],
                ['className' => 'text-center text-bold'],
                null, // Alasan
                ['className' => 'text-center'], // Status
                null, // Diproses Oleh
                null, // Tgl Diproses
            ],
            'paging' => true,
            'lengthMenu' => [ 10, 25, 50, 100 ],
            'searching' => true, // Aktifkan pencarian
            'info' => true,
            'responsive' => true,
            'autoWidth' => false,
        ];
        @endphp

        {{-- Mengisi data untuk tabel --}}
        @foreach($adjustments as $adj)
            @php
                // Tentukan warna selisih
                $qty_class = $adj->quantity == 0 ? 'text-muted' : ($adj->quantity > 0 ? 'text-success' : 'text-danger');
                $qty_text = $adj->quantity == 0 ? '0' : ($adj->quantity > 0 ? '+' . $adj->quantity : $adj->quantity);

                // Tentukan badge status
                $status_badge = '';
                switch ($adj->status) {
                    case 'approved': $status_badge = '<span class="badge badge-success">Approved</span>'; break;
                    case 'rejected': $status_badge = '<span class="badge badge-danger">Rejected</span>'; break;
                    default: $status_badge = '<span class="badge badge-warning">Pending</span>'; break;
                }
                
                $config['data'][] = [
                    $adj->created_at->format('Y-m-d H:i'),
                    $adj->requestor->name ?? '-',
                    $adj->item->name ?? 'N/A',
                    $adj->stock_in_system,
                    $adj->stock_physical,
                    '<span class="' . $qty_class . '">' . $qty_text . '</span>',
                    $adj->notes,
                    $status_badge,
                    $adj->approver->name ?? '-', // Nama approver jika ada
                    $adj->approved_at ? $adj->approved_at->format('Y-m-d H:i') : '-', // Tanggal approve/reject
                ];
            @endphp
        @endforeach

        {{-- Tampilkan Datatable --}}
        <x-adminlte-datatable id="table-adjustment-history" :heads="$heads" :config="$config" striped hoverable bordered compressed with-buttons/>
    
    </x-adminlte-card>
@stop

{{-- Tambahkan notifikasi pop-up SweetAlert --}}
@push('js')
    <x-notification-alert />
@endpush