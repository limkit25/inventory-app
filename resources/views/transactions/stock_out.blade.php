@extends('adminlte::page')

@section('title', 'Stok Pakai / Keluar')

@section('content_header')
    <h1>Stok Pakai / Keluar</h1>
@stop

@section('content')
    <x-adminlte-card theme="danger" icon="fas fa-arrow-circle-up" title="Form Stok Pakai">
        
        {{-- Ini akan mengarah ke route 'stock.out.store' dengan method POST --}}
        <form action="{{ route('stock.out.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    {{-- Dropdown Pilih Barang --}}
                    {{-- Controller sudah memfilter barang yang stoknya > 0 --}}
                    <x-adminlte-select2 name="item_id" label="Barang yang Akan Dipakai" required>
                        <option value="" disabled selected>Pilih Barang...</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }} (Stok Saat Ini: {{ $item->current_stock }})
                            </option>
                        @endforeach
                    </x-adminlte-select2>
                </div>
                <div class="col-md-6">
                    {{-- Input Jumlah Keluar --}}
                    <x-adminlte-input name="quantity" label="Jumlah Pakai" type="number" min="1" 
                        placeholder="Jumlah yang dipakai"
                        value="{{ old('quantity') }}" required />
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    {{-- Input Catatan --}}
                    <x-adminlte-textarea name="notes" label="Catatan (Keperluan, dll)">
                        {{ old('notes') }}
                    </x-adminlte-textarea>
                </div>
            </div>

            {{-- Tombol Simpan dan Batal --}}
            <x-adminlte-button type="submit" label="Simpan (Pakai Stok)" theme="danger" icon="fas fa-save"/>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Batal</a>
            
        </form>
        
    </x-adminlte-card>
@stop