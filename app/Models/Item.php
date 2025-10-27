<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
   protected $fillable = ['name', 'sku', 'unit', 'current_stock', 'average_cost', 'category_id'];
public function stockMovements() { return $this->hasMany(StockMovement::class); }
public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
