@extends('adminlte::page')
@section('title', 'Tambah Role Baru')
@section('content_header') <h1>Tambah Role Baru</h1> @stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-plus" title="Form Tambah Role">
    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <x-adminlte-input name="name" label="Nama Role" placeholder="Contoh: Manajer"
            value="{{ old('name') }}" required />

        <hr>
        <h5>Izin (Permissions)</h5>
        <div class="row">
            @foreach($permissions as $permission)
                <div class="col-md-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}">
                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                            {{ $permission->name }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>

        <hr>
        <x-adminlte-button type="submit" label="Simpan" theme="primary" icon="fas fa-save"/>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</x-adminlte-card>
@stop