<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Kita perlukan ini untuk validasi

class ItemController extends Controller
{
    /**
     * Menampilkan daftar semua barang.
     */
    public function index()
    {
        $items = Item::orderBy('name')->get();
        return view('master.items.index', compact('items'));
    }

    /**
     * Menampilkan form untuk membuat barang baru.
     */
    public function create()
    {
        return view('master.items.create');
    }

    /**
     * Menyimpan barang baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:items,name',
            'unit' => 'required|string|max:50',
            'sku' => 'nullable|string|max:100|unique:items,sku',
        ]);

        Item::create($validatedData);

        return redirect()->route('items.index')
                         ->with('success', 'Barang baru berhasil ditambahkan.');
    }

    /**
     * ===================================================================
     * FUNGSI BARU DARI SINI
     * ===================================================================
     */

    /**
     * Menampilkan form untuk mengedit barang.
     * Method: GET
     * Route: /items/{item}/edit (name: items.edit)
     */
    public function edit(Item $item)
    {
        // $item otomatis diambil oleh Laravel (Route Model Binding)
        return view('master.items.edit', compact('item'));
    }

    /**
     * Mengupdate data barang di database.
     * Method: PUT/PATCH
     * Route: /items/{item} (name: items.update)
     */
    public function update(Request $request, Item $item)
    {
        // 1. Validasi input
        $validatedData = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('items')->ignore($item->id), // Unik, tapi abaikan ID saat ini
            ],
            'unit' => 'required|string|max:50',
            'sku' => [
                'nullable', 'string', 'max:100',
                Rule::unique('items')->ignore($item->id), // Unik, tapi abaikan ID saat ini
            ],
        ]);
        
        // 2. Tidak mengizinkan update 'current_stock' atau 'average_cost' dari sini.
        // Data itu HANYA boleh diubah oleh InventoryService.
        // Kita hanya ambil data yang aman untuk di-update.
        $safeData = [
            'name' => $validatedData['name'],
            'unit' => $validatedData['unit'],
            'sku' => $validatedData['sku'],
        ];

        // 3. Update data di database
        $item->update($safeData);

        // 4. Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('items.index')
                         ->with('success', 'Data barang berhasil diperbarui.');
    }

    /**
     * Menghapus data barang dari database.
     * Method: DELETE
     * Route: /items/{item} (name: items.destroy)
     */
    public function destroy(Item $item)
    {
        try {
            // Hapus barang
            $item->delete();
            
            // Redirect dengan pesan sukses
            return redirect()->route('items.index')
                             ->with('success', 'Data barang berhasil dihapus.');

        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani jika ada error foreign key (barang sudah dipakai di transaksi)
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1451){
                return redirect()->route('items.index')
                                 ->with('error', 'Gagal menghapus! Barang ini sudah digunakan dalam transaksi stok.');
            }
            
            // Error lainnya
            return redirect()->route('items.index')
                             ->with('error', 'Gagal menghapus data barang: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail satu barang (tidak kita pakai saat ini,
     * tapi wajib ada untuk Route::resource jika tidak di-exclude)
     */
    public function show(Item $item)
    {
        // Arahkan saja ke halaman edit
        return redirect()->route('items.edit', $item->id);
    }
}