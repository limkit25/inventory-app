@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard Utama</h1>
@stop

@section('content')
    <p>Selamat datang di Aplikasi Inventory!</p>
    <hr>

    {{-- =============================================== --}}
    {{-- BAGIAN MASTER DATA & PENGGUNA --}}
    {{-- =============================================== --}}
    {{-- Tampilkan heading jika punya salah satu izin --}}
    @canany(['manage-master-data', 'manage-users'])
        <h4><i class="fas fa-database"></i> Master Data & User</h4>
        <div class="row mb-3"> {{-- Beri sedikit jarak antar bagian --}}
            {{-- Kotak Menu Master Data --}}
            @can('manage-master-data')
                <div class="col-lg-3 col-6 text-sm">
                    <x-adminlte-small-box title="Master Data" text="Barang, Vendor, Kategori"
                        icon="fas fa-database fa-sm text-dark"
                        theme="info"
                        url="{{ route('items.index') }}" url-text="Lihat Data"/>
                </div>
            @endcan

            {{-- Kotak Menu Manajemen Pengguna --}}
            @can('manage-users')
                <div class="col-lg-3 col-6 text-sm">
                    <x-adminlte-small-box title="Manajemen User" text="Kelola User & Role"
                        icon="fas fa-users-cog fa-sm text-white"
                        theme="secondary"
                        url="{{ route('users.index') }}" url-text="Kelola User"/>
                </div>
            @endcan
        </div>
        <hr>
    @endcanany

    {{-- =============================================== --}}
    {{-- BAGIAN TRANSAKSI --}}
    {{-- =============================================== --}}
    {{-- Tampilkan heading jika punya salah satu izin --}}
    @canany('perform-transactions')
        <h4><i class="fas fa-exchange-alt"></i> Transaksi</h4>
        <div class="row mb-3">
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

        </div>
        <hr>
    @endcanany

    @canany('approve-adjustments')
        <h4><i class="fas fa-database"></i> Persetujuan SO </h4>
        <div class="row mb-3"> {{-- Beri sedikit jarak antar bagian --}}
            {{-- Kotak Menu Master Data --}}
            @can('approve-adjustments')
                <div class="col-lg-3 col-6 text-sm">
                    <x-adminlte-small-box title="Persetujuan Stok" text="Approve/Reject SO"
                        icon="fas fa-check-circle fa-sm text-white"
                        theme="teal"
                        url="{{ route('adjustments.index') }}" url-text="Lihat Pengajuan"/>
                </div>
            @endcan
        </div>
        <hr>
    @endcanany

    {{-- =============================================== --}}
    {{-- BAGIAN LAPORAN --}}
    {{-- =============================================== --}}
    {{-- Tampilkan heading jika punya izin --}}
    @can('view-reports')
        <h4><i class="fas fa-chart-bar"></i> Laporan</h4>
        <div class="row mb-3">
            {{-- Kotak Menu Laporan (Stok Akhir) --}}
            <div class="col-lg-3 col-6 text-sm">
                <x-adminlte-small-box title="Laporan Stok" text="Stok Akhir, Kartu Stok"
                    icon="fas fa-warehouse fa-sm text-white" {{-- Ikon gudang --}}
                    theme="primary"
                    url="{{ route('reports.inventory.index') }}" url-text="Lihat Stok"/>
            </div>

            {{-- Kotak Menu Riwayat SO --}}
            <div class="col-lg-3 col-6 text-sm">
                <x-adminlte-small-box title="Riwayat SO" text="Lihat Semua Penyesuaian"
                    icon="fas fa-history fa-sm text-dark" {{-- Ikon history --}}
                    theme="lightblue"
                    url="{{ route('adjustments.history') }}" url-text="Lihat Riwayat"/>
            </div>
        </div>
        {{-- Tidak perlu <hr> di bagian terakhir --}}
    @endcan

@stop