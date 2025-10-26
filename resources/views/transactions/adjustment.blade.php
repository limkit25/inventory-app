@extends('adminlte::page')

@section('title', 'Penyesuaian Stok')

@section('content_header')
    <h1>Penyesuaian Stok (Stock Opname)</h1>
@stop

@section('content')
    <x-adminlte-card theme="warning" icon="fas fa-edit" title="Form Penyesuaian Stok">
        
        <p class="text-muted">
            Gunakan form ini untuk mencocokkan stok sistem dengan stok fisik di gudang.
            Masukkan jumlah stok <strong>fisik (hasil hitungan)</strong> yang baru.
        </p>

        <form action="{{ route('stock.adjustment.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    {{-- Dropdown Pilih Barang --}}
                    <x-adminlte-select2 name="item_id" label="Barang yang Disesuaikan" required>
                        <option value="" disabled selected>Pilih Barang...</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }} (Stok Sistem: {{ $item->current_stock }})
                            </option>
                        @endforeach
                    </x-adminlte-select2>
                </div>
                <div class="col-md-6">
                    {{-- Input Stok Fisik Baru --}}
                    <x-adminlte-input name="new_physical_stock" label="Stok Fisik Baru (Hasil Hitungan)" 
                        type="number" min="0" 
                        placeholder="Jumlah fisik di gudang"
                        value="{{ old('new_physical_stock') }}" required />
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    {{-- Input Catatan (Wajib) --}}
                    <x-adminlte-textarea name="notes" label="Alasan Penyesuaian (Wajib)" 
                        placeholder="Contoh: Hasil Stock Opname, Barang Rusak, Barang Hilang, dll."
                        required>
                        {{ old('notes') }}
                    </x-adminlte-textarea>
                </div>
            </div>

            <x-adminlte-button type="submit" label="Simpan Penyesuaian" theme="warning" icon="fas fa-save"/>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Batal</a>
            
        </form>
        
    </x-adminlte-card>
@stop