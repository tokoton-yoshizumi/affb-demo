<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function commissions()
    {
        return $this->hasMany(ProductCommission::class);
    }
}
