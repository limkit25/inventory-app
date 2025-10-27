@extends('adminlte::page')

@section('title', 'Stok Pakai / Keluar')

@section('content_header')
    <h1>Stok Pakai / Keluar (Multi-Item)</h1>
@stop

@section('content')
<form action="{{ route('stock.out.store') }}" method="POST">
    @csrf

    {{-- 1. KARTU HEADER (KEPERLUAN, TANGGAL) --}}
    <x-adminlte-card theme="danger" icon="fas fa-arrow-circle-up" title="Detail Pengeluaran Barang">

        {{-- BLOK UNTUK MENAMPILKAN ERROR VALIDASI --}}
        @if ($errors->any())
            <x-adminlte-alert theme="danger" title="Kesalahan Validasi">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-adminlte-alert>
        @endif

        <div class="row">
            <div class="col-md-6">
                {{-- Kita gunakan field 'notes' untuk keperluan --}}
                <x-adminlte-input name="notes" label="Keperluan / Keterangan" 
                    placeholder="Contoh: Pemakaian Poli Gigi, Untuk Pasien X, dll" 
                    value="{{ old('notes') }}" required />
            </div>
            <div class="col-md-6">
                {{-- Input Tanggal Keluar (Opsional, default hari ini) --}}
                <x-adminlte-input name="movement_date" label="Tanggal Keluar" type="date" 
                    value="{{ old('movement_date', date('Y-m-d')) }}" required />
            </div>
        </div>
    </x-adminlte-card>

    {{-- 2. KARTU DETAIL (DAFTAR BARANG) --}}
    <x-adminlte-card theme="primary" icon="fas fa-boxes" title="Daftar Barang yang Dipakai">

        <div class="table-responsive">
            <table class="table table-bordered" id="item-details-table">
                <thead>
                    <tr>
                        <th style="width: 70%;">Nama Barang</th>
                        <th style="width: 20%;">Jumlah Pakai</th>
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
                                <button type="button" class="btn btn-danger btn-sm btn-remove-item">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <button type="button" id="btn-add-item" class="btn btn-primary mt-3">
            <i class="fas fa-plus"></i> Tambah Baris Barang
        </button>

    </x-adminlte-card>

    {{-- 3. TOMBOL SIMPAN UTAMA --}}
    <div class="row">
        <div class="col-md-12">
            <x-adminlte-button type="submit" label="Simpan Stok Keluar" theme="danger" icon="fas fa-save" class="btn-lg"/>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-lg">Batal</a>
        </div>
    </div>

</form>
@stop

{{-- 4. JAVASCRIPT UNTUK TAMBAH/HAPUS BARIS (Sama seperti Stok Masuk) --}}
@push('js')
<script>
$(document).ready(function() {
    let rowIndex = {{ old('items') ? count(old('items')) : 1 }};

    // Fungsi untuk inisialisasi Select2
    function initializeSelect2(element) {
        element.select2({
            placeholder: 'Pilih Barang...',
            width: '100%'
        });
    }

    // Inisialisasi Select2 untuk baris yang sudah ada
    $('.item-select').each(function() {
        initializeSelect2($(this));
    });

    // Tombol Tambah Baris
    $('#btn-add-item').on('click', function() {
        var newRow = $('.item-row:first').clone();
        newRow.find('input').val('');
        newRow.find('.item-select').val(null).trigger('change');
        newRow.find('.select2-container').remove();

        // Ubah atribut 'name'
        newRow.find('select').attr('name', 'items[' + rowIndex + '][item_id]');
        newRow.find('input[type="number"]').attr('name', 'items[' + rowIndex + '][quantity]');

        $('#item-details-table tbody').append(newRow);
        initializeSelect2(newRow.find('.item-select'));
        rowIndex++;
    });

    // Tombol Hapus Baris
    $('#item-details-table').on('click', '.btn-remove-item', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
        } else {
             Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Minimal harus ada satu baris barang.',
            });
        }
    });
});
</script>
@endpush