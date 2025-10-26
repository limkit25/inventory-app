@extends('adminlte::page')
@section('title', 'Edit Pengguna')
@section('content_header') <h1>Edit Pengguna: {{ $user->name }}</h1> @stop

@section('content')
<x-adminlte-card theme="warning" icon="fas fa-edit" title="Form Edit Pengguna">
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <x-adminlte-input name="name" label="Nama" placeholder="Nama Lengkap" 
            value="{{ old('name', $user->name) }}" required />
        
        <x-adminlte-input name="email" type="email" label="Email" placeholder="email@example.com" 
            value="{{ old('email', $user->email) }}" required />
        
        <p class="text-muted small">Kosongkan password jika Anda tidak ingin mengubahnya.</p>
        <x-adminlte-input name="password" type="password" label="Password Baru (Opsional)" 
            placeholder="Password Baru" autocomplete="new-password" />
        
        <x-adminlte-input name="password_confirmation" type="password" label="Konfirmasi Password Baru" 
            placeholder="Konfirmasi Password Baru" />

        {{-- Dropdown untuk Roles --}}
        {{-- Ini adalah bagian penting untuk assign/sync roles --}}
        <x-adminlte-select2 name="roles[]" label="Hak Akses (Role)" required>
            <option value="" disabled>Pilih Role...</option>
            @foreach($roles as $role)
                <option value="{{ $role }}" {{ in_array($role, $userRoles) ? 'selected' : '' }}>
                    {{ $role }}
                </option>
            @endforeach
        </x-adminlte-select2>
        
        <x-adminlte-button type="submit" label="Update" theme="primary" icon="fas fa-save"/>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</x-adminlte-card>
@stop