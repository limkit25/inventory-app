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
            'order' => [[0, 'desc']], // Urutkan berdasarkan tanggal, terbaru di atas
            'paging' => true,
            'searching' => false,
            'info' => false,
            'responsive' => true,
        ];
        @endphp

        <x-adminlte-datatable id="table-stockcard" :heads="$heads" :config="$config" striped hoverable bordered compressed>
            @foreach($movements as $mov)
                <tr>
                    <td>{{ $mov->movement_date }}</td>
                    <td>
                        @if($mov->type == 'in')
                            <span class="badge badge-success">Stok Masuk</span>
                        @else
                            <span class="badge badge-danger">Stok Pakai</span>
                        @endif
                    </td>
                    <td>
                        {{-- Jika stok masuk, tampilkan nama vendor. Jika stok keluar, tampilkan catatan --}}
                        {{ $mov->vendor->name ?? $mov->notes ?? '-' }}
                    </td>
                    
                    {{-- Pisahkan kolom Masuk dan Keluar --}}
                    <td>
                        @if($mov->type == 'in')
                            <strong>+{{ $mov->quantity }}</strong>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($mov->type == 'out')
                            <strong>-{{ $mov->quantity }}</strong>
                        @else
                            -
                        @endif
                    </td>
                    
                    <td class="text-right">Rp {{ number_format($mov->cost_per_unit, 2, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($mov->total_cost, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </x-adminlte-datatable>
    </x-adminlte-card>
@stop