@extends('adminlte::page')

@section('title', 'Edit Vendor')

@section('content_header')
    {{-- Judul dinamis --}}
    <h1>Edit Vendor: {{ $vendor->name }}</h1>
@stop

@section('content')
    <x-adminlte-card theme="warning" icon="fas fa-edit" title="Form Edit Vendor">
        
        {{-- 
          - Form ini mengarah ke route 'vendors.update'
          - Kita menggunakan $vendor->id untuk memberi tahu ID vendor mana yang di-update
        --}}
        <form action="{{ route('vendors.update', $vendor->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- PENTING: Method 'PUT' atau 'PATCH' wajib untuk update --}}
            
            <div class="row">
                <div class="col-md-6">
                    {{-- 
                      - 'value' diisi dengan data lama
                      - old('name', $vendor->name) berarti:
                      - "Ambil data 'name' dari validasi error (old), 
                      - jika tidak ada, ambil data dari database ($vendor->name)"
                    --}}
                    <x-adminlte-input name="name" label="Nama Vendor" 
                        placeholder="Contoh: PT. Sinar Jaya" 
                        value="{{ old('name', $vendor->name) }}" required>
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-truck"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>
                <div class="col-md-6">
                    {{-- Input Kontak Person --}}
                    <x-adminlte-input name="contact_person" label="Kontak Person" 
                        placeholder="Nama kontak"
                        value="{{ old('contact_person', $vendor->contact_person) }}">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-user"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                     {{-- Input Telepon --}}
                    <x-adminlte-input name="phone" label="Telepon" 
                        placeholder="0812..."
                        value="{{ old('phone', $vendor->phone) }}">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-phone"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    {{-- Input Alamat --}}
                    <x-adminlte-textarea name="address" label="Alamat" 
                        placeholder="Alamat lengkap vendor...">
                        {{ old('address', $vendor->address) }}
                    </x-adminlte-textarea>
                </div>
            </div>

            {{-- Tombol Update dan Batal --}}
            <x-adminlte-button type="submit" label="Update" theme="primary" icon="fas fa-save"/>
            <a href="{{ route('vendors.index') }}" class="btn btn-secondary">Batal</a>
            
        </form>
        
    </x-adminlte-card>
@stop