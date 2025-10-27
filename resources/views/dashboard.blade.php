@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard Utama</h1>
@stop

@section('content')
    <p>Selamat datang di Aplikasi Inventory!</p>
    <hr>
    <h4>Menu Utama:</h4>

    <div class="row">

        {{-- Kotak Menu Master Data --}}
        @can('manage-master-data')
            {{-- Tambahkan text-sm di sini --}}
            <div class="col-lg-3 col-6 text-sm"> 
                <x-adminlte-small-box title="Master Data" text="Barang, Vendor, Kategori" 
                    {{-- Tambahkan fa-sm di sini --}}
                    icon="fas fa-database fa-sm text-dark" 
                    theme="info" 
                    url="{{ route('items.index') }}" url-text="Lihat Data"/>
            </div>
        @endcan

        {{-- Kotak Menu Stok Masuk --}}
        @can('perform-transactions')
            <div class="col-lg-3 col-6 text-sm">
                <x-adminlte-small-box title="Stok Masuk" text="Input Pembelian Baru" 
                    icon="fas fa-arrow-circle-down fa-sm text-white" 
                    theme="success" 
                    url="{{ route('stock.in.create') }}" url-text="Input Stok Masuk"/>
            </div>
        @endcan

        {{-- Kotak Menu Stok Keluar --}}
        @can('perform-transactions')
            <div class="col-lg-3 col-6 text-sm">
                <x-adminlte-small-box title="Stok Keluar" text="Input Pemakaian Barang" 
                    icon="fas fa-arrow-circle-up fa-sm text-white" 
                    theme="danger" 
                    url="{{ route('stock.out.create') }}" url-text="Input Stok Keluar"/>
            </div>
        @endcan
        
        {{-- Kotak Menu Penyesuaian Stok --}}
        @can('perform-transactions')
             <div class="col-lg-3 col-6 text-sm">
                <x-adminlte-small-box title="Penyesuaian Stok" text="Ajukan Stock Opname" 
                    icon="fas fa-edit fa-sm text-dark" 
                    theme="warning" 
                    url="{{ route('stock.adjustment.create') }}" url-text="Buat Pengajuan"/>
            </div>
        @endcan

        {{-- Kotak Menu Persetujuan Stok --}}
        @can('approve-adjustments')
             <div class="col-lg-3 col-6 text-sm">
                <x-adminlte-small-box title="Persetujuan Stok" text="Approve/Reject SO" 
                    icon="fas fa-check-circle fa-sm text-white" 
                    theme="teal" 
                    url="{{ route('adjustments.index') }}" url-text="Lihat Pengajuan"/>
            </div>
        @endcan

        {{-- Kotak Menu Laporan --}}
        @can('view-reports')
            <div class="col-lg-3 col-6 text-sm">
                <x-adminlte-small-box title="Laporan" text="Stok Akhir, Kartu Stok, dll" 
                    icon="fas fa-chart-bar fa-sm text-white" 
                    theme="primary" 
                    url="{{ route('reports.inventory.index') }}" url-text="Lihat Laporan"/>
            </div>
        @endcan

        @can('view-reports') {{-- Izinnya sama dengan Laporan lain --}}
            <div class="col-lg-3 col-6 text-sm">
                <x-adminlte-small-box title="Riwayat SO" text="Lihat Semua Penyesuaian"
                    icon="fas fa-history fa-sm text-dark" {{-- Ikon & warna berbeda --}}
                    theme="lightblue" {{-- Tema warna berbeda --}}
                    url="{{ route('adjustments.history') }}" url-text="Lihat Riwayat"/>
            </div>
        @endcan

        {{-- Kotak Menu Manajemen Pengguna --}}
         @can('manage-users')
            <div class="col-lg-3 col-6 text-sm">
                <x-adminlte-small-box title="Manajemen Pengguna" text="Kelola User & Role" 
                    icon="fas fa-users-cog fa-sm text-white" 
                    theme="secondary" 
                    url="{{ route('users.index') }}" url-text="Kelola Pengguna"/>
            </div>
        @endcan

    </div> 
    {{-- Akhir div class="row" --}}
@stop