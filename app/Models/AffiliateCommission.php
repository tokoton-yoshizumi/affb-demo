<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'affiliate_link_id',
        'amount',
        'product_name',
        'session_id',
        'is_paid',
        'paid_at',
        'status',
        'created_at',
        'reward_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function affiliateLink()
    {
        return $this->belongsTo(AffiliateLink::class);
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
