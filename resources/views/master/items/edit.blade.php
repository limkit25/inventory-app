@extends('adminlte::page')

@section('title', 'Edit Barang')

@section('content_header')
    <h1>Edit Barang: {{ $item->name }}</h1>
@stop

@section('content')
    <x-adminlte-card theme="warning" icon="fas fa-edit" title="Form Edit Barang">
        
        {{-- Form ini mengarah ke route 'items.update' --}}
        <form action="{{ route('items.update', $item->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- PENTING: Method 'PUT' untuk update --}}
            
            <div class="row">
                <div class="col-md-6">
                    {{-- Input Nama Barang (Wajib) --}}
                    <x-adminlte-input name="name" label="Nama Barang" 
                        placeholder="Contoh: Barang A" 
                        value="{{ old('name', $item->name) }}" required>
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-box"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>
                <div class="col-md-6">
                    {{-- Input Satuan (Wajib) --}}
                    <x-adminlte-input name="unit" label="Satuan" 
                        placeholder="Contoh: pcs, kg, box, unit"
                        value="{{ old('unit', $item->unit) }}" required>
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-tag"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                     {{-- Input SKU (Opsional) --}}
                    <x-adminlte-input name="sku" label="SKU (Kode Barang)" 
                        placeholder="Contoh: BRG-001 (Opsional)"
                        value="{{ old('sku', $item->sku) }}">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-barcode"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>
            </div>

            <p class="text-danger small">
                * Anda tidak bisa mengubah "Stok" atau "Harga" dari halaman ini.
                <br>
                * Perubahan stok dan harga hanya terjadi melalui menu
                <strong>"Stok Masuk"</strong> atau <strong>"Stok Pakai"</strong>.
            </p>
            <hr>

            {{-- Tombol Update dan Batal --}}
            <x-adminlte-button type="submit" label="Update Barang" theme="primary" icon="fas fa-save"/>
            <a href="{{ route('items.index') }}" class="btn btn-secondary">Batal</a>
            
        </form>
        
    </x-adminlte-card>
@stop