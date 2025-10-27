<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            
            // Barang apa yang disesuaikan
            $table->foreignId('item_id')->constrained('items');
            
            // Siapa yang mengajukan
            $table->foreignId('user_id')->constrained('users');
            
            // Status pengajuan
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            // Detail penyesuaian
            $table->integer('stock_in_system'); // Stok di sistem (sebelum)
            $table->integer('stock_physical');  // Stok fisik (hasil hitung)
            $table->integer('quantity');        // Selisihnya (bisa + atau -)
            
            // Alasan
            $table->text('notes');
            
            // Detail persetujuan
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps(); // created_at (kapan diajukan)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
