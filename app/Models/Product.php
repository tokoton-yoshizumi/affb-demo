<?php

namespace App\Models;

use App\Models\ProductCommission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    // フォームからの入力を許可するカラムを指定
    protected $fillable = [
        'name',
        'description',
        'price',
        'price_id',
        'url',
        'tracking_code_status',
        'thank_you_url',
        'status'
    ];
    public function commissions()
    {
        return $this->hasMany(ProductCommission::class);
    }

    public function affiliateLinks()
    {
        return $this->hasMany(AffiliateLink::class);
    }
}
