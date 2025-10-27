@extends('adminlte::page')
@section('title', 'Tambah Barang Baru')
@section('content_header') <h1>Tambah Barang Baru</h1> @stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-plus" title="Form Tambah Barang">
    <form action="{{ route('items.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <x-adminlte-input name="name" label="Nama Barang" 
                    placeholder="Contoh: Barang A" value="{{ old('name') }}" required>
                    <x-slot name="prependSlot"><div class="input-group-text"><i class="fas fa-box"></i></div></x-slot>
                </x-adminlte-input>
            </div>

            {{-- ====================================== --}}
            {{-- TAMBAHKAN DROPDOWN KATEGORI DI SINI --}}
            {{-- ====================================== --}}
            <div class="col-md-6">
                <x-adminlte-select2 name="category_id" label="Kategori Barang" required>
                    <option value="" disabled selected>Pilih Kategori...</option>
                    @foreach($categories as $id => $name)
                        <option value="{{ $id }}" {{ old('category_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </x-adminlte-select2>
            </div>
            {{-- ====================================== --}}
        </div>

        <div class="row">
             <div class="col-md-6">
                <x-adminlte-input name="unit" label="Satuan" 
                    placeholder="Contoh: pcs, kg, box, unit" value="{{ old('unit') }}" required>
                     <x-slot name="prependSlot"><div class="input-group-text"><i class="fas fa-tag"></i></div></x-slot>
                </x-adminlte-input>
            </div>
            <div class="col-md-6">
                <x-adminlte-input name="sku" label="SKU (Kode Barang)" 
                    placeholder="Contoh: BRG-001 (Opsional)" value="{{ old('sku') }}">
                     <x-slot name="prependSlot"><div class="input-group-text"><i class="fas fa-barcode"></i></div></x-slot>
                </x-adminlte-input>
            </div>
        </div>

        <p class="text-muted small">
            * Anda tidak perlu mengisi "Stok Awal" atau "Harga Rata-rata" di sini.
            <br>
            * Stok dan Harga akan terisi otomatis saat Anda melakukan transaksi <strong>"Stok Masuk / Awal"</strong>.
        </p>
        <hr>

        <x-adminlte-button type="submit" label="Simpan Barang" theme="primary" icon="fas fa-save"/>
        <a href="{{ route('items.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</x-adminlte-card>
@stop