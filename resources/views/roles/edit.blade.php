@extends('adminlte::page')
@section('title', 'Edit Role')
@section('content_header') <h1>Edit Role: {{ $role->name }}</h1> @stop

@section('content')
<x-adminlte-card theme="warning" icon="fas fa-edit" title="Form Edit Role">

    {{-- Tampilkan Error Validasi --}}
    @if ($errors->any())
        <x-adminlte-alert theme="danger" title="Kesalahan Validasi">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-adminlte-alert>
    @endif

    <form action="{{ route('roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- =============================================== --}}
        {{-- PASTIKAN INPUT INI ADA DAN LENGKAP --}}
        {{-- =============================================== --}}
        <x-adminlte-input name="name" label="Nama Role" placeholder="Contoh: Manajer"
            value="{{ old('name', $role->name) }}" required {{ $role->name === 'Admin' ? 'readonly' : '' }} />
        {{-- =============================================== --}}


        <hr>
        <h5>Izin (Permissions)</h5>
        <div class="row">
            @foreach($permissions as $permission)
                <div class="col-md-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}"
                            {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                            {{ $role->name === 'Admin' ? 'onclick="return false;"' : '' }} >
                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                            {{ $permission->name }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>

        <hr>
        <x-adminlte-button type="submit" label="Update" theme="primary" icon="fas fa-save"/>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</x-adminlte-card>
@stop