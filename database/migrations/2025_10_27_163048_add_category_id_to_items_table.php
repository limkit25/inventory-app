<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Tambahkan kolom category_id setelah kolom 'unit'
            // constrained() otomatis membuat foreign key ke tabel 'categories'
            // nullable() agar barang lama tidak error
            // nullOnDelete() agar jika kategori dihapus, barangnya tidak ikut terhapus
            $table->foreignId('category_id')
                  ->nullable()
                  ->after('unit')
                  ->constrained('categories')
                  ->nullOnDelete(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Hapus foreign key dulu sebelum hapus kolom
            $table->dropForeign(['category_id']); 
            $table->dropColumn('category_id');
        });
    }
};
