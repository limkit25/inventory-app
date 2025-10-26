@extends('adminlte::page')
@section('title', 'Tambah Barang Baru')
@section('content_header') <h1>Tambah Barang Baru</h1> @stop

@section('content')
<x-adminlte-card title="Form Barang Baru" theme="primary" icon="fas fa-box">
    <form action="{{ route('items.store') }}" method="POST">
        @csrf
        <x-adminlte-input name="name" label="Nama Barang" placeholder="Barang A" required />
        <x-adminlte-input name="unit" label="Satuan" placeholder="pcs" required />
        <x-adminlte-input name="sku" label="SKU (Opsional)" placeholder="A-001" />

        <x-adminlte-button type="submit" label="Simpan" theme="primary" icon="fas fa-save"/>
    </form>
</x-adminlte-card>
@stop