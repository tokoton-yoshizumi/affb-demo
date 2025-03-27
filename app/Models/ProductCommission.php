<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCommission extends Model
{

    protected $fillable = [
        'product_id',
        'affiliate_type',
        'affiliate_type_id',
        'fixed_commission',
        'fixed_commission_on_form',
        'fixed_commission_on_payment',
        'commission_rate',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function affiliateType()
    {
        return $this->belongsTo(AffiliateType::class);
    }
}
