@extends('adminlte::page')
@section('title', 'Tambah Pengguna Baru')
@section('content_header') <h1>Tambah Pengguna Baru</h1> @stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-plus" title="Form Tambah Pengguna">
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <x-adminlte-input name="name" label="Nama" placeholder="Nama Lengkap" value="{{ old('name') }}" required />
        
        <x-adminlte-input name="email" type="email" label="Email" placeholder="email@example.com" value="{{ old('email') }}" required />
        
        <x-adminlte-input name="password" type="password" label="Password" placeholder="Password" required autocomplete="new-password" />
        
        <x-adminlte-input name="password_confirmation" type="password" label="Konfirmasi Password" placeholder="Konfirmasi Password" required />

        {{-- Dropdown untuk Roles --}}
        <x-adminlte-select2 name="roles[]" label="Hak Akses (Role)" required>
            <option value="" disabled selected>Pilih Role...</option>
            @foreach($roles as $role)
                <option value="{{ $role }}">{{ $role }}</option>
            @endforeach
        </x-adminlte-select2>

        <x-adminlte-button type="submit" label="Simpan" theme="primary" icon="fas fa-save"/>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</x-adminlte-card>
@stop