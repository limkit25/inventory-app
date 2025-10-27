@extends('adminlte::page')

@section('title', 'Laporan Stok Akhir')

@section('content_header')
    <h1>Laporan Stok Akhir (Metode Rata-rata)</h1>
@stop

@section('content')
    <x-adminlte-card theme="info" icon="fas fa-warehouse" title="Posisi Nilai Persediaan Saat Ini">

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

        <a href="{{ route('reports.inventory.export') }}" class="btn btn-success mb-3">
            <i class="fas fa-file-excel"></i> Ekspor ke Excel
        </a>

        @php
        $heads = [
            'ID',
            'SKU', // <-- TAMBAHAN BARU
            'Nama Barang',
            'Satuan',
            'Stok Akhir',
            'Harga Rata-rata (HPP)', 
            'Total Nilai Persediaan', 
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $config = [
            'data' => [],
            'order' => [[1, 'asc']],
            'columns' => [
                ['data' => 'id', 'width' => '5%'],
                ['data' => 'sku'], // <-- TAMBAHAN BARU
                ['data' => 'name'],
                ['data' => 'unit'],
                ['data' => 'stock', 'className' => 'text-bold'],
                ['data' => 'avg_cost', 'className' => 'text-right'],
                ['data' => 'total_value', 'className' => 'text-bold text-right'],
                ['data' => 'actions', 'orderable' => false, 'searchable' => false],
            ],
            'paging' => true,
            'lengthMenu' => [ 10, 25, 50, 100 ],
            'searching' => true,
            'info' => true,
            'responsive' => true,
            'autoWidth' => false,
        ];
        @endphp

        @foreach($items as $item)
            @php
                $btnDetail = '<a href="' . route('reports.stockcard.show', $item->id) . '" 
                                 class="btn btn-xs btn-default text-info mx-1 shadow" 
                                 title="Lihat Kartu Stok">
                                 <i class="fa fa-lg fa-fw fa-eye"></i>
                             </a>';

                $config['data'][] = [
                    'id' => $item->id,
                    'sku' => $item->sku ?? '-', // <-- TAMBAHAN BARU
                    'name' => $item->name,
                    'unit' => $item->unit,
                    'stock' => $item->current_stock,
                    'avg_cost' => 'Rp ' . number_format($item->average_cost, 2, ',', '.'),
                    'total_value' => 'Rp ' . number_format($item->current_stock * $item->average_cost, 2, ',', '.'),
                    'actions' => '<nobr>' . $btnDetail . '</nobr>',
                ];
            @endphp
        @endforeach

        <x-adminlte-datatable id="table-inventory" :heads="$heads" :config="$config" striped hoverable bordered compressed with-buttons/>
    
    </x-adminlte-card>
@stop