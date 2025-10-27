@extends('adminlte::page')

@section('title', 'Stok Masuk / Awal')

@section('content_header')
    <h1>Stok Masuk / Pembelian (Multi-Item)</h1>
@stop

@section('content')
<form action="{{ route('stock.in.store') }}" method="POST">
    @csrf
    
    {{-- 1. KARTU HEADER (VENDOR, INVOICE, TANGGAL) --}}
    <x-adminlte-card theme="success" icon="fas fa-plus-circle" title="Detail Penerimaan Barang">
        
        {{-- =============================================== --}}
        {{-- BLOK UNTUK MENAMPILKAN ERROR VALIDASI --}}
        {{-- =============================================== --}}
        @if ($errors->any())
            <x-adminlte-alert theme="danger" title="Kesalahan Validasi">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-adminlte-alert>
        @endif
        {{-- =============================================== --}}


        <div class="row">
            <div class="col-md-4">
                {{-- Dropdown Pilih Vendor --}}
                <x-adminlte-select2 name="vendor_id" label="Vendor/Pemasok" required>
                    <option value="" disabled selected>Pilih Vendor...</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->name }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>
            <div class="col-md-4">
                <x-adminlte-input name="invoice_number" label="Nomor Invoice" 
                    placeholder="Nomor Invoice/Surat Jalan" value="{{ old('invoice_number') }}" />
            </div>
            <div class="col-md-4">
                <x-adminlte-input name="movement_date" label="Tanggal Terima" type="date" 
                    value="{{ old('movement_date', date('Y-m-d')) }}" required />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <x-adminlte-textarea name="notes" label="Catatan (Opsional)">
                    {{ old('notes') }}
                </x-adminlte-textarea>
            </div>
        </div>
    </x-adminlte-card>

    {{-- 2. KARTU DETAIL (DAFTAR BARANG) --}}
    <x-adminlte-card theme="primary" icon="fas fa-boxes" title="Daftar Barang yang Masuk">
        
        {{-- =============================================== --}}
        {{-- DIV UNTUK MEMBUAT TABEL RESPONSIVE DI HP --}}
        {{-- =============================================== --}}
        <div class="table-responsive">

            <table class="table table-bordered" id="item-details-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Nama Barang</th>
                        <th style="width: 20%;">Jumlah</th>
                        <th style="width: 30%;">Harga Beli Satuan (Rp)</th>
                        <th style="width: 10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- JIKA ADA VALIDATION ERROR, TAMPILKAN LAGI INPUT LAMA --}}
                    @if(old('items'))
                        @foreach(old('items') as $index => $item)
                            <tr class="item-row">
                                <td>
                                    <select name="items[{{$index}}][item_id]" class="form-control item-select" required>
                                        <option value="" disabled selected>Pilih Barang...</option>
                                        @foreach($items as $masterItem)
                                            <option value="{{ $masterItem->id }}" {{ $masterItem->id == $item['item_id'] ? 'selected' : '' }}>
                                                {{ $masterItem->name }} (Stok: {{ $masterItem->current_stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[{{$index}}][quantity]" class="form-control" 
                                        min="1" placeholder="Jumlah" value="{{ $item['quantity'] }}" required>
                                </td>
                                <td>
                                    <input type="number" name="items[{{$index}}][cost_per_unit]" class="form-control" 
                                        min="0" step="0.01" placeholder="Harga Beli" value="{{ $item['cost_per_unit'] }}" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm btn-remove-item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        {{-- BARIS KOSONG PERTAMA (TEMPLATE) --}}
                        <tr class="item-row">
                            <td>
                                <select name="items[0][item_id]" class="form-control item-select" required>
                                    <option value="" disabled selected>Pilih Barang...</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->name }} (Stok: {{ $item->current_stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[0][quantity]" class="form-control" 
                                    min="1" placeholder="Jumlah" required>
                            </td>
                            <td>
                                <input type="number" name="items[0][cost_per_unit]" class="form-control" 
                                    min="0" step="0.01" placeholder="Harga Beli" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm btn-remove-item">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>

        </div> {{-- <-- PENUTUP .table-responsive --}}
        {{-- =============================================== --}}

        <button type="button" id="btn-add-item" class="btn btn-primary mt-3">
            <i class="fas fa-plus"></i> Tambah Baris Barang
        </button>
        
    </x-adminlte-card>

    {{-- 3. TOMBOL SIMPAN UTAMA --}}
    <div class="row">
        <div class="col-md-12">
            <x-adminlte-button type="submit" label="Simpan Stok Masuk" theme="success" icon="fas fa-save" class="btn-lg"/>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-lg">Batal</a>
        </div>
    </div>

</form>
@stop

{{-- 4. JAVASCRIPT UNTUK TAMBAH/HAPUS BARIS --}}
@push('js')
<script>
$(document).ready(function() {
    let rowIndex = {{ old('items') ? count(old('items')) : 1 }};

    // Fungsi untuk inisialisasi Select2 pada elemen baru
    function initializeSelect2(element) {
        element.select2({
            placeholder: 'Pilih Barang...',
            width: '100%'
        });
    }

    // Inisialisasi Select2 untuk baris yang sudah ada (termasuk dari 'old' input)
    $('.item-select').each(function() {
        initializeSelect2($(this));
    });

    // Tombol Tambah Baris
    $('#btn-add-item').on('click', function() {
        // Dapatkan template baris pertama
        var newRow = $('.item-row:first').clone();
        
        // Bersihkan nilai input di baris baru
        newRow.find('input').val('');
        
        // Hancurkan Select2 lama sebelum meng-clone
        // Lalu re-inisialisasi
        newRow.find('.item-select').val(null).trigger('change');
        newRow.find('.select2-container').remove();
        
        // Ubah atribut 'name' agar unik (items[1], items[2], dst.)
        newRow.find('select').attr('name', 'items[' + rowIndex + '][item_id]');
        newRow.find('input[type="number"]:eq(0)').attr('name', 'items[' + rowIndex + '][quantity]');
        newRow.find('input[type="number"]:eq(1)').attr('name', 'items[' + rowIndex + '][cost_per_unit]');

        // Tambahkan baris baru ke tabel
        $('#item-details-table tbody').append(newRow);
        
        // Inisialisasi Select2 pada baris baru
        initializeSelect2(newRow.find('.item-select'));
        
        rowIndex++;
    });

    // Tombol Hapus Baris
    $('#item-details-table').on('click', '.btn-remove-item', function() {
        // Jangan hapus baris terakhir
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
        } else {
            Swal.fire({ // Ganti alert() biasa dengan SweetAlert
                icon: 'warning',
                title: 'Oops...',
                text: 'Minimal harus ada satu baris barang.',
            });
        }
    });
});
</script>
@endpush