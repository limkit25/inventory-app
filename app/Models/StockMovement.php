<?php

namespace App\Models; // <-- Pastikan namespace-nya App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'vendor_id',
        'type',
        'quantity',
        'cost_per_unit',
        'total_cost',
        'movement_date',
        'notes',
        'invoice_number', // <-- Pastikan ini ada
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'movement_date' => 'datetime',
    ];

    /**
     * Relasi ke master barang.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relasi ke master vendor.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}