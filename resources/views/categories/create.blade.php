@extends('adminlte::page')
@section('title', 'Tambah Kategori Baru')
@section('content_header') <h1>Tambah Kategori Baru</h1> @stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-plus" title="Form Tambah Kategori">
    <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <x-adminlte-input name="name" label="Nama Kategori" placeholder="Contoh: Alat Medis"
            value="{{ old('name') }}" required />

        <x-adminlte-textarea name="description" label="Deskripsi (Opsional)"
            placeholder="Deskripsi singkat tentang kategori ini...">{{ old('description') }}</x-adminlte-textarea>

        <x-adminlte-button type="submit" label="Simpan" theme="primary" icon="fas fa-save"/>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</x-adminlte-card>
@stop