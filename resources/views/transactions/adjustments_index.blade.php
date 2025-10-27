@extends('adminlte::page')

@section('title', 'Persetujuan Stok')

@section('content_header')
    <h1>Persetujuan Penyesuaian Stok</h1>
@stop

@section('content')
    <x-adminlte-card theme="info" icon="fas fa-check-circle" title="Daftar Pengajuan (Pending)">
        
        <p class="text-muted">
            Berikut adalah daftar pengajuan penyesuaian stok yang menunggu persetujuan Anda.
        </p>

        @php
        $heads = [
            'Tanggal',
            'Diajukan Oleh',
            'Nama Barang',
            'Stok Sistem',
            'Stok Fisik',
            'Selisih',
            'Alasan',
            ['label' => 'Actions', 'no-export' => true, 'width' => 10],
        ];

        $config = [
            'data' => [],
            'order' => [[0, 'desc']], // Terbaru di atas
            'columns' => [
                null, null, null,
                ['className' => 'text-center'],
                ['className' => 'text-center text-bold'],
                ['className' => 'text-center text-bold'],
                null,
                ['orderable' => false, 'searchable' => false],
            ],
        ];
        @endphp

        {{-- Mengisi data untuk tabel --}}
        @forelse($adjustments as $adj)
            @php
                // Tentukan warna selisih
                $qty_class = $adj->quantity > 0 ? 'text-success' : 'text-danger';
                $qty_text = $adj->quantity > 0 ? '+' . $adj->quantity : $adj->quantity;

                // Tombol Approve
                $btnApprove = '<form action="' . route('adjustments.approve', $adj->id) . '" method="POST" style="display:inline;">
                                  ' . csrf_field() . '
                                  <button type="submit" class="btn btn-xs btn-success mx-1 shadow" title="Approve" onclick="return confirm(\'Anda yakin ingin MENYETUJUI pengajuan ini?\')">
                                      <i class="fa fa-lg fa-fw fa-check"></i>
                                  </button>
                              </form>';
                
                // Tombol Reject
                $btnReject = '<form action="' . route('adjustments.reject', $adj->id) . '" method="POST" style="display:inline;">
                                  ' . csrf_field() . '
                                  <button type="submit" class="btn btn-xs btn-danger mx-1 shadow" title="Reject" onclick="return confirm(\'Anda yakin ingin MENOLAK pengajuan ini?\')">
                                      <i class="fa fa-lg fa-fw fa-times"></i>
                                  </button>
                              </form>';
                
                $config['data'][] = [
                    $adj->created_at->format('Y-m-d H:i'),
                    $adj->requestor->name, // Ambil dari relasi
                    $adj->item->name,     // Ambil dari relasi
                    $adj->stock_in_system,
                    $adj->stock_physical,
                    '<span class="' . $qty_class . '">' . $qty_text . '</span>',
                    $adj->notes,
                    '<nobr>' . $btnApprove . $btnReject . '</nobr>',
                ];
            @endphp
        @empty
            {{-- Datatable akan otomatis menampilkan "No matching records found" --}}
        @endforelse

        {{-- Tampilkan Datatable --}}
        <x-adminlte-datatable id="table-adjustments" :heads="$heads" :config="$config" striped hoverable bordered compressed/>
    
    </x-adminlte-card>
@stop

{{-- Tambahkan notifikasi pop-up SweetAlert --}}
@push('js')
    <x-notification-alert />
@endpush