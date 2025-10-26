@extends('adminlte::page')

{{-- Ini akan mengisi konten utama halaman Anda --}}
@section('content')
    {{ $slot }}
@stop

{{-- 
    Jika Anda ingin mengontrol header halaman (cth: "Dashboard") 
    dari file view anak (seperti dashboard.blade.php), 
    Anda bisa tambahkan ini:
--}}
@hasSection('content_header')
    @section('content_header')
        @yield('content_header')
    @stop
@endif

{{-- =============================================== --}}
{{-- TAMBAHKAN BLOK INI --}}
{{-- =============================================== --}}
{{-- Ini akan "mendorong" script SweetAlert ke bagian footer
     layout AdminLTE di semua halaman --}}
{{-- @push('js')
    <x-notification-alert />
@endpush --}}
{{-- =============================================== --}}