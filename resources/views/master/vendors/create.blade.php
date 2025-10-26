@extends('adminlte::page')

@section('title', 'Tambah Vendor Baru')

@section('content_header')
    <h1>Tambah Vendor Baru</h1>
@stop

@section('content')
    <x-adminlte-card theme="primary" icon="fas fa-plus" title="Form Tambah Vendor">
        
        {{-- Ini akan mengarah ke route 'vendors.store' dengan method POST --}}
        <form action="{{ route('vendors.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    {{-- Input Nama Vendor (Wajib) --}}
                    <x-adminlte-input name="name" label="Nama Vendor" 
                        placeholder="Contoh: PT. Sinar Jaya" 
                        value="{{ old('name') }}" required>
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
                        value="{{ old('contact_person') }}">
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
                        value="{{ old('phone') }}">
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
                        {{ old('address') }}
                    </x-adminlte-textarea>
                </div>
            </div>

            {{-- Tombol Simpan dan Batal --}}
            <x-adminlte-button type="submit" label="Simpan" theme="primary" icon="fas fa-save"/>
            <a href="{{ route('vendors.index') }}" class="btn btn-secondary">Batal</a>
            
        </form>
        
    </x-adminlte-card>
@stop