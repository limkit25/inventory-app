<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
    'item_id', 'vendor_id', 'type', 'quantity', 
    'cost_per_unit', 'total_cost', 'movement_date', 'notes'
];
public function item() { return $this->belongsTo(Item::class); }
public function vendor() { return $this->belongsTo(Vendor::class); }
}
