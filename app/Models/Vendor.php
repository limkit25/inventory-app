<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = ['name', 'contact_person', 'phone', 'address'];
public function stockMovements() { return $this->hasMany(StockMovement::class); }
}
