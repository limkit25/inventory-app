<?php

namespace App\Http\Controllers;

use App\Models\Vendor;        // Impor model Vendor
use Illuminate\Http\Request; // Impor kelas Request
use Illuminate\Validation\Rule; // (Opsional, untuk validasi unik yang lebih kompleks)

class VendorController extends Controller
{
    /**
     * Menampilkan daftar semua vendor.
     * Method: GET
     * Route: /vendors (name: vendors.index)
     */
    public function index()
    {
        // Ambil semua data vendor, urutkan berdasarkan nama
        $vendors = Vendor::orderBy('name')->get();
        
        // Tampilkan view dan kirim data $vendors
        return view('master.vendors.index', compact('vendors'));
    }

    /**
     * Menampilkan form untuk membuat vendor baru.
     * Method: GET
     * Route: /vendors/create (name: vendors.create)
     */
    public function create()
    {
        // Tampilkan view form create
        return view('master.vendors.create');
    }

    /**
     * Menyimpan vendor baru ke database.
     * Method: POST
     * Route: /vendors (name: vendors.store)
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:vendors,name', // Pastikan nama vendor unik
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        // 2. Buat data baru di database
        Vendor::create($validatedData);

        // 3. Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('vendors.index')
                         ->with('success', 'Vendor baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit vendor.
     * Method: GET
     * Route: /vendors/{vendor}/edit (name: vendors.edit)
     */
    public function edit(Vendor $vendor)
    {
        // Tampilkan view form edit dan kirim data $vendor yang ingin diedit
        // Laravel otomatis mengambil data vendor berdasarkan ID di URL (Route Model Binding)
        return view('master.vendors.edit', compact('vendor'));
    }

    /**
     * Mengupdate data vendor yang ada di database.
     * Method: PUT/PATCH
     * Route: /vendors/{vendor} (name: vendors.update)
     */
    public function update(Request $request, Vendor $vendor)
    {
        // 1. Validasi input
        $validatedData = $request->validate([
            // Pastikan validasi unik mengabaikan ID vendor saat ini
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('vendors')->ignore($vendor->id),
            ],
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        // 2. Update data di database
        $vendor->update($validatedData);

        // 3. Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('vendors.index')
                         ->with('success', 'Data vendor berhasil diperbarui.');
    }

    /**
     * Menghapus data vendor dari database.
     * Method: DELETE
     * Route: /vendors/{vendor} (name: vendors.destroy)
     */
    public function destroy(Vendor $vendor)
    {
        try {
            // Hapus vendor
            $vendor->delete();
            
            // Redirect dengan pesan sukses
            return redirect()->route('vendors.index')
                             ->with('success', 'Data vendor berhasil dihapus.');
                             
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani jika ada error foreign key (vendor sudah dipakai di transaksi)
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1451){
                return redirect()->route('vendors.index')
                                 ->with('error', 'Gagal menghapus! Vendor ini sudah digunakan dalam transaksi stok.');
            }
            
            // Error lainnya
            return redirect()->route('vendors.index')
                             ->with('error', 'Gagal menghapus data vendor: ' . $e->getMessage());
        }
    }
}