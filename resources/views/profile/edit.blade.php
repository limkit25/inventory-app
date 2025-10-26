<x-app-layout>
    {{-- Atur Judul Halaman AdminLTE --}}
    @section('title', 'My Profile')

    {{-- Atur Header Konten AdminLTE --}}
    @section('content_header')
        <h1 class="m-0 text-dark">Profile</h1>
    @stop

    {{-- Konten Utama (akan masuk ke $slot di app.blade.php) --}}
    <div class="container-fluid">
        <div class="row">
            {{-- Kolom untuk Info & Ganti Password --}}
            <div class="col-lg-8">
                
                {{-- Kartu Update Informasi Profil --}}
                <div class="mb-4">
                    @include('profile.partials.update-profile-information-form')
                </div>

                {{-- Kartu Update Password --}}
                <div class="mb-4">
                    @include('profile.partials.update-password-form')
                </div>

            </div>

            {{-- Kolom untuk Hapus Akun --}}
            <div class="col-lg-4">
                {{-- Kartu Hapus Akun --}}
                <div class="mb-4">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>