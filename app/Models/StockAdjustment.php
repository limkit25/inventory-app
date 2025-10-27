<?php

namespace App\Models;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'user_id',
        'status',
        'stock_in_system',
        'stock_physical',
        'quantity',
        'notes',
        'approved_by',
        'approved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * Relasi ke barang yang disesuaikan.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relasi ke pengguna yang MENGAJUKAN.
     */
    public function requestor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke pengguna yang MENYETUJUI.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}