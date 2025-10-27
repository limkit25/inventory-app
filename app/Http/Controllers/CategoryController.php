<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar semua kategori.
     */
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Menampilkan form untuk membuat kategori baru.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Menyimpan kategori baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        Category::create($validatedData);

        return redirect()->route('categories.index')
                         ->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit kategori.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Mengupdate data kategori di database.
     */
    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('categories')->ignore($category->id),
            ],
            'description' => 'nullable|string',
        ]);

        $category->update($validatedData);

        return redirect()->route('categories.index')
                         ->with('success', 'Data kategori berhasil diperbarui.');
    }

    /**
     * Menghapus data kategori dari database.
     */
    public function destroy(Category $category)
    {
        // PENTING: Kita menggunakan nullOnDelete di migrasi items,
        // jadi kita tidak perlu khawatir error foreign key di sini.
        // Jika Anda ingin mencegah penghapusan jika kategori masih dipakai,
        // tambahkan pengecekan: if ($category->items()->count() > 0) { ... }
        try {
            $category->delete();
            return redirect()->route('categories.index')
                             ->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
             return redirect()->route('categories.index')
                             ->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
}