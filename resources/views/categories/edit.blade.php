@extends('adminlte::page')
@section('title', 'Edit Kategori')
@section('content_header') <h1>Edit Kategori: {{ $category->name }}</h1> @stop

@section('content')
<x-adminlte-card theme="warning" icon="fas fa-edit" title="Form Edit Kategori">
    <form action="{{ route('categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')
        <x-adminlte-input name="name" label="Nama Kategori" placeholder="Contoh: Alat Medis"
            value="{{ old('name', $category->name) }}" required />

        <x-adminlte-textarea name="description" label="Deskripsi (Opsional)"
            placeholder="Deskripsi singkat tentang kategori ini...">{{ old('description', $category->description) }}</x-adminlte-textarea>

        <x-adminlte-button type="submit" label="Update" theme="primary" icon="fas fa-save"/>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</x-adminlte-card>
@stop