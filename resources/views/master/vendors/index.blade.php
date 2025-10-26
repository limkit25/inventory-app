@extends('adminlte::page')

@section('title', 'Data Vendor')

@section('content_header')
    <h1>Master Data Vendor</h1>
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

        <x-adminlte-button label="Tambah Vendor Baru" 
                           theme="primary" 
                           icon="fas fa-plus" 
                           class="mb-3"
                           onclick="window.location='{{ route('vendors.create') }}'"/>

        @php
        $heads = [
            'ID', 'Nama Vendor', 'Kontak Person', 'Telepon',
            ['label' => 'Actions', 'no-export' => true, 'width' => 10],
        ];
        $config = ['data' => [], 'order' => [[1, 'asc']], 'columns' => [
                ['data' => 'id', 'width' => '5%'], ['data' => 'name'], ['data' => 'contact'],
                ['data' => 'phone'], ['data' => 'actions', 'orderable' => false, 'searchable' => false, 'width' => '10%'],
            ],
        ];
        @endphp

        @foreach($vendors as $vendor)
            @php
                $btnEdit = '<a href="' . route('vendors.edit', $vendor->id) . '" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </a>';
                $btnDelete = '<form action="' . route('vendors.destroy', $vendor->id) . '" method="POST" style="display:inline;">
                                  ' . csrf_field() . method_field('DELETE') . '
                                  <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Hapus" onclick="return confirm(\'Anda yakin ingin menghapus vendor ini?\')">
                                      <i class="fa fa-lg fa-fw fa-trash"></i>
                                  </button>
                              </form>';
                $config['data'][] = [
                    'id' => $vendor->id, 'name' => $vendor->name, 'contact' => $vendor->contact_person,
                    'phone' => $vendor->phone, 'actions' => '<nobr>' . $btnEdit . $btnDelete . '</nobr>',
                ];
            @endphp
        @endforeach

        <x-adminlte-datatable id="table-vendors" :heads="$heads" :config="$config" striped hoverable bordered with-buttons/>
    </x-adminlte-card>
@stop
