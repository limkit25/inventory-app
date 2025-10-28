@extends('adminlte::page')
@section('title', 'Hak Akses (Roles)')
@section('content_header') <h1>Manajemen Hak Akses (Roles)</h1> @stop

@section('content')
<x-adminlte-card theme="primary" theme-mode="outline">

    {{-- Notifikasi --}}
    @if(session('success'))
        <x-adminlte-alert theme="success" title="Berhasil" dismissable>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif
    @if(session('error'))
        <x-adminlte-alert theme="danger" title="Gagal" dismissable>
            {{ session('error') }}
        </x-adminlte-alert>
    @endif

    {{-- Tombol Tambah --}}
    <x-adminlte-button label="Tambah Role Baru"
                       theme="primary" icon="fas fa-plus" class="mb-3"
                       onclick="window.location='{{ route('roles.create') }}'"/>

    {{-- Tabel Roles --}}
    @php
    $heads = ['ID', 'Nama Role', 'Izin (Permissions)', ['label' => 'Actions', 'no-export' => true, 'width' => 10]];
    $config = ['data' => [], 'order' => [[1, 'asc']], 'columns' => [
        ['data' => 'id', 'width' => '5%'], ['data' => 'name'], ['data' => 'permissions'],
        ['data' => 'actions', 'orderable' => false, 'searchable' => false, 'width' => '10%'],
    ]];
    @endphp

    @foreach($roles as $role)
        @php
            $btnEdit = '<a href="' . route('roles.edit', $role->id) . '" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                            <i class="fa fa-lg fa-fw fa-pen"></i>
                        </a>';
            // Tombol Hapus hanya muncul jika bukan role 'Admin'
            $btnDelete = $role->name !== 'Admin' ?
                         '<form action="' . route('roles.destroy', $role->id) . '" method="POST" style="display:inline;">
                              ' . csrf_field() . method_field('DELETE') . '
                              <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Hapus" onclick="return confirm(\'Anda yakin ingin menghapus role ini?\')">
                                  <i class="fa fa-lg fa-fw fa-trash"></i>
                              </button>
                          </form>' : '';

            // Tampilkan permissions sebagai badge
            $permissions = '';
            foreach ($role->permissions->pluck('name') as $permission) {
                $permissions .= '<span class="badge badge-info mr-1">' . $permission . '</span>';
            }

            $config['data'][] = [
                'id' => $role->id, 'name' => $role->name, 'permissions' => $permissions ?: '-',
                'actions' => '<nobr>' . $btnEdit . $btnDelete . '</nobr>',
            ];
        @endphp
    @endforeach

    <x-adminlte-datatable id="table-roles" :heads="$heads" :config="$config" striped hoverable bordered with-buttons/>
</x-adminlte-card>
@stop