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
        Schema::create('stock_movements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('item_id')->constrained('items');
    $table->foreignId('vendor_id')->nullable()->constrained('vendors');
    $table->enum('type', ['in', 'out']);
    $table->integer('quantity');
    $table->decimal('cost_per_unit', 15, 2)->nullable(); // Harga beli (in) / Harga avg (out)
    $table->decimal('total_cost', 15, 2);
    $table->dateTime('movement_date');
    $table->text('notes')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
