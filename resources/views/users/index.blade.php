@extends('adminlte::page')

@section('title', 'Manajemen Pengguna')

@section('content_header')
    <h1>Manajemen Pengguna</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" theme-mode="outline">

    {{-- KEMBALIKAN BLOK INI --}}
    @if(session('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif
    @if(session('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissable>
            {{ session('error') }}
        </x-adminlte-alert>
    @endif
    {{-- SAMPAI SINI --}}

    <x-adminlte-button label="Tambah Pengguna Baru" 
                       theme="primary" 
                       icon="fas fa-plus" 
                       class="mb-3"
                       onclick="window.location='{{ route('users.create') }}'"/>

    @php
    $heads = [
        'ID', 'Nama', 'Email', 'Hak Akses (Role)', 
        ['label' => 'Actions', 'no-export' => true, 'width' => 10]
    ];
    $config = ['data' => [], 'order' => [[1, 'asc']], 'columns' => [
            ['data' => 'id', 'width' => '5%'], ['data' => 'name'], ['data' => 'email'],
            ['data' => 'roles'], ['data' => 'actions', 'orderable' => false, 'searchable' => false, 'width' => '10%'],
        ]
    ];
    @endphp

    @foreach($users as $user)
        @php
            $btnEdit = '<a href="' . route('users.edit', $user->id) . '" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                            <i class="fa fa-lg fa-fw fa-pen"></i>
                        </a>';
            $btnDelete = '<form action="' . route('users.destroy', $user->id) . '" method="POST" style="display:inline;">
                              ' . csrf_field() . method_field('DELETE') . '
                              <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Hapus" onclick="return confirm(\'Anda yakin ingin menghapus pengguna ini?\')">
                                  <i class="fa fa-lg fa-fw fa-trash"></i>
                              </button>
                          </form>';
            $roles = '';
            foreach ($user->getRoleNames() as $role) {
                $roles .= '<span class="badge badge-primary mr-1">' . $role . '</span>';
            }
            $config['data'][] = [
                'id' => $user->id, 'name' => $user->name, 'email' => $user->email,
                'roles' => $roles, 'actions' => '<nobr>' . $btnEdit . $btnDelete . '</nobr>',
            ];
        @endphp
    @endforeach

    <x-adminlte-datatable id="table-users" :heads="$heads" :config="$config" striped hoverable bordered with-buttons/>
</x-adminlte-card>
@stop
