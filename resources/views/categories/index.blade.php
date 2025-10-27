@extends('adminlte::page')
@section('title', 'Kategori Barang')
@section('content_header') <h1>Master Kategori Barang</h1> @stop

@section('content')
<x-adminlte-card theme="primary" theme-mode="outline">

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

    <x-adminlte-button label="Tambah Kategori Baru"
                       theme="primary" icon="fas fa-plus" class="mb-3"
                       onclick="window.location='{{ route('categories.create') }}'"/>

    @php
    $heads = ['ID', 'Nama Kategori', 'Deskripsi', ['label' => 'Actions', 'no-export' => true, 'width' => 10]];
    $config = ['data' => [], 'order' => [[1, 'asc']], 'columns' => [
        ['data' => 'id', 'width' => '5%'], ['data' => 'name'], ['data' => 'desc'],
        ['data' => 'actions', 'orderable' => false, 'searchable' => false, 'width' => '10%'],
    ]];
    @endphp

    @foreach($categories as $category)
        @php
            $btnEdit = '<a href="' . route('categories.edit', $category->id) . '" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                            <i class="fa fa-lg fa-fw fa-pen"></i>
                        </a>';
            $btnDelete = '<form action="' . route('categories.destroy', $category->id) . '" method="POST" style="display:inline;">
                              ' . csrf_field() . method_field('DELETE') . '
                              <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Hapus" onclick="return confirm(\'Anda yakin ingin menghapus kategori ini?\')">
                                  <i class="fa fa-lg fa-fw fa-trash"></i>
                              </button>
                          </form>';
            $config['data'][] = [
                'id' => $category->id, 'name' => $category->name, 'desc' => $category->description,
                'actions' => '<nobr>' . $btnEdit . $btnDelete . '</nobr>',
            ];
        @endphp
    @endforeach

    <x-adminlte-datatable id="table-categories" :heads="$heads" :config="$config" striped hoverable bordered with-buttons/>
</x-adminlte-card>
@stop

@push('js') <x-notification-alert /> @endpush