@extends('adminlte::page')

@section('title', 'Data Barang')

@section('content_header')
    <h1>Master Data Barang</h1>
@stop

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

        <x-adminlte-button label="Tambah Barang Baru" 
                           theme="primary" 
                           icon="fas fa-plus" 
                           class="mb-3"
                           onclick="window.location='{{ route('items.create') }}'"/>

        @php
        $heads = [
            'ID',
            'SKU', // <-- TAMBAHAN BARU
            'Nama Barang',
            'Kategori',
            'Satuan',
            'Stok Saat Ini',
            'Harga Rata-rata (HPP)',
            ['label' => 'Actions', 'no-export' => true, 'width' => 10],
        ];

        $config = [
            'data' => [], 
            'order' => [[1, 'asc']],
            'columns' => [
                ['data' => 'id', 'width' => '5%'],
                ['data' => 'sku'], // <-- TAMBAHAN BARU
                ['data' => 'name'],
                ['data' => 'category'],
                ['data' => 'unit'],
                ['data' => 'stock'],
                ['data' => 'cost', 'className' => 'text-right'],
                ['data' => 'actions', 'orderable' => false, 'searchable' => false, 'width' => '10%'],
            ],
        ];
        @endphp

        @foreach($items as $item)
            @php
                $btnEdit = '<a href="' . route('items.edit', $item->id) . '" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </a>';
                $btnDelete = '<form action="' . route('items.destroy', $item->id) . '" method="POST" style="display:inline;">
                                  ' . csrf_field() . method_field('DELETE') . '
                                  <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Hapus" onclick="return confirm(\'Anda yakin ingin menghapus barang ini?\')">
                                      <i class="fa fa-lg fa-fw fa-trash"></i>
                                  </button>
                              </form>';
                
                $config['data'][] = [
                    'id' => $item->id,
                    'sku' => $item->sku ?? '-', // <-- TAMBAHAN BARU
                    'name' => $item->name,
                    'category' => $item->category->name ?? '-', // <-- AMBIL NAMA KATEGORI DARI RELASI
                    'unit' => $item->unit,
                    'stock' => $item->current_stock,
                    'cost' => 'Rp ' . number_format($item->average_cost, 2, ',', '.'),
                    'actions' => '<nobr>' . $btnEdit . $btnDelete . '</nobr>',
                ];
            @endphp
        @endforeach

        <x-adminlte-datatable id="table-items" :heads="$heads" :config="$config" striped hoverable bordered with-buttons/>
    </x-adminlte-card>
@stop