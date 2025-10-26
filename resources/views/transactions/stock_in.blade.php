@extends('adminlte::page')
@section('title', 'Stok Masuk / Stok Awal')
@section('content_header') <h1>Stok Masuk / Stok Awal</h1> @stop

@section('content')
<x-adminlte-card title="Form Stok Masuk" theme="success" icon="fas fa-plus-circle">
    <form action="{{ route('stock.in.store') }}" method="POST">
        @csrf
        <x-adminlte-select2 name="item_id" label="Barang" required>
            <option value="" disabled selected>Pilih Barang...</option>
            @foreach($items as $item)
                <option value="{{ $item->id }}">{{ $item->name }} (Stok: {{ $item->current_stock }})</option>
            @endforeach
        </x-adminlte-select2>

        <x-adminlte-select2 name="vendor_id" label="Vendor/Pemasok" required>
            <option value="" disabled selected>Pilih Vendor...</option>
            @foreach($vendors as $vendor)
                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
            @endforeach
        </x-adminlte-select2>

        <x-adminlte-input name="quantity" label="Jumlah Masuk" type="number" min="1" required />

        {{-- Ini adalah Harga Beli --}}
        <x-adminlte-input name="cost_per_unit" label="Harga Beli per Satuan (Rp)" type="number" min="0" step="0.01" required />

        <x-adminlte-textarea name="notes" label="Catatan" />

        <x-adminlte-button type="submit" label="Simpan Stok Masuk" theme="success" icon="fas fa-save"/>
    </form>
</x-adminlte-card>
@stop