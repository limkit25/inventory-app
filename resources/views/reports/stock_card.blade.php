@extends('adminlte::page')

@section('title', 'Kartu Stok')

@section('content_header')
    {{-- Judul dinamis berdasarkan nama barang --}}
    <h1>Kartu Stok: {{ $item->name }}</h1>
@stop

@section('content')
    {{-- Box Ringkasan --}}
    <div class="row">
        <div class="col-md-4">
            <x-adminlte-info-box title="Stok Saat Ini" 
                text="{{ $item->current_stock }} {{ $item->unit }}" 
                icon="fas fa-warehouse" theme="info"/>
        </div>
        <div class="col-md-4">
            <x-adminlte-info-box title="Harga Rata-rata (HPP)" 
                text="Rp {{ number_format($item->average_cost, 2, ',', '.') }}" 
                icon="fas fa-tag" theme="warning"/>
        </div>
        <div class="col-md-4">
            <x-adminlte-info-box title="Total Nilai Persediaan" 
                text="Rp {{ number_format($item->current_stock * $item->average_cost, 2, ',', '.') }}" 
                icon="fas fa-dollar-sign" theme="success"/>
        </div>
    </div>

    {{-- Box Tabel Riwayat --}}
    <x-adminlte-card theme="primary" theme-mode="outline" title="Riwayat Pergerakan Stok">
        
        {{-- =============================================== --}}
        {{-- FORM FILTER TANGGAL BARU KITA --}}
        {{-- =============================================== --}}
        <form method="GET" action="{{ route('reports.stockcard.show', $item->id) }}" class="mb-4">
            <div class="row">
                <div class="col-md-5">
                    {{-- 
                      - Kita gunakan type="date" standar browser, tidak perlu plugin
                      - value="{{ $startDate ?? '' }}" akan mengisi kembali tanggal
                        yang sudah difilter
                    --}}
                    <x-adminlte-input name="start_date" label="Tanggal Mulai" type="date"
                        value="{{ $startDate ?? '' }}" />
                </div>
                <div class="col-md-5">
                    <x-adminlte-input name="end_date" label="Tanggal Selesai" type="date"
                        value="{{ $endDate ?? '' }}" />
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('reports.stockcard.show', $item->id) }}" class="btn btn-default" title="Reset Filter">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
        <hr>
        {{-- =============================================== --}}
        {{-- AKHIR DARI FORM FILTER --}}
        {{-- =============================================== --}}


        <a href="{{ route('reports.inventory.index') }}" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Kembali ke Laporan Stok Akhir
        </a>

        @php
        $heads = [
            'Tanggal',
            'Tipe',
            'Keterangan / Vendor',
            ['label' => 'Masuk', 'width' => 8],
            ['label' => 'Keluar', 'width' => 8],
            ['label' => 'Harga Satuan', 'width' => 15],
            ['label' => 'Total Nilai', 'width' => 15],
        ];

        $config = [
            'data' => [], // <-- KITA KOSONGKAN, KARENA DIISI LOOP DI BAWAH
            'order' => [[0, 'desc']], // Urutkan berdasarkan tanggal, terbaru di atas
            'paging' => true,
            'searching' => false,
            'info' => false,
            'responsive' => true,
        ];
        @endphp

        {{-- Mengisi data untuk Datatable --}}
        @forelse($movements as $mov)
            @php
                $tipe = $mov->type == 'in' ? '<span class="badge badge-success">Stok Masuk</span>' : '<span class="badge badge-danger">Stok Pakai</span>';
                $keterangan = $mov->vendor->name ?? $mov->notes ?? '-';
                $masuk = $mov->type == 'in' ? '<strong>+' . $mov->quantity . '</strong>' : '-';
                $keluar = $mov->type == 'out' ? '<strong>-' . $mov->quantity . '</strong>' : '-';
                
                $config['data'][] = [
                    $mov->movement_date,
                    $tipe,
                    $keterangan,
                    $masuk,
                    $keluar,
                    'Rp ' . number_format($mov->cost_per_unit, 2, ',', '.'),
                    'Rp ' . number_format($mov->total_cost, 2, ',', '.'),
                ];
            @endphp
        @empty
            {{-- Biarkan kosong, datatable akan menampilkan "No matching records found" --}}
        @endforelse

        {{-- Tampilkan Datatable --}}
        <x-adminlte-datatable id="table-stockcard" :heads="$heads" :config="$config" striped hoverable bordered compressed/>
    
    </x-adminlte-card>
@stop