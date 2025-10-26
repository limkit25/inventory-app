@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    {{-- 1. Baris InfoBox --}}
    <div class="row">
        <div class="col-lg-4 col-6">
            <x-adminlte-info-box title="Total Nilai Persediaan" 
                text="Rp {{ number_format($totalValue, 2, ',', '.') }}" 
                icon="fas fa-dollar-sign" theme="success"/>
        </div>
        <div class="col-lg-4 col-6">
            <x-adminlte-info-box title="Total Master Barang" 
                text="{{ $totalItems }} Jenis" 
                icon="fas fa-box" theme="info"/>
        </div>
        <div class="col-lg-4 col-6">
            <x-adminlte-info-box title="Total Vendor" 
                text="{{ $totalVendors }} Vendor" 
                icon="fas fa-truck" theme="purple"/>
        </div>
    </div>

    {{-- 2. Baris Konten Utama (Stok Menipis & Transaksi Terakhir) --}}
    <div class="row">

        {{-- Kolom Kiri: Stok Menipis --}}
        <div class="col-lg-6">
            <x-adminlte-card theme="danger" theme-mode="outline" 
                             icon="fas fa-exclamation-triangle" title="Barang Stok Menipis (< 10)">

                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Stok Saat Ini</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockItems as $item)
                                <tr>
                                    <td>
                                        <a href="{{ route('reports.stockcard.show', $item->id) }}">
                                            {{ $item->name }}
                                        </a>
                                    </td>
                                    <td><strong>{{ $item->current_stock }}</strong></td>
                                    <td>{{ $item->unit }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada barang yang stoknya menipis.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </x-adminlte-card>
        </div>

        {{-- Kolom Kanan: Transaksi Terakhir --}}
        <div class="col-lg-6">
            <x-adminlte-card theme="info" theme-mode="outline" 
                             icon="fas fa-history" title="5 Transaksi Terakhir">

                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Barang</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentMovements as $mov)
                                <tr>
                                    <td>{{ $mov->movement_date }}</td>
                                    <td>
                                        @if($mov->type == 'in')
                                            <span class="badge badge-success">Masuk</span>
                                        @else
                                            <span class="badge badge-danger">Keluar</span>
                                        @endif
                                    </td>
                                    <td>{{ $mov->item->name }}</td>
                                    <td>
                                        @if($mov->type == 'in')
                                            +{{ $mov->quantity }}
                                        @else
                                            -{{ $mov->quantity }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </x-adminlte-card>
        </div>

    </div>
@stop