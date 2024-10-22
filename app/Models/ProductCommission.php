<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCommission extends Model
{
    protected $fillable = ['product_id', 'affiliate_type', 'fixed_commission', 'commission_rate', 'affiliate_type_id',];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
