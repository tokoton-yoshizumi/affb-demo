<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    // テーブル名（デフォルトは 'customers'）
    protected $table = 'customers';

    // このモデルに対して大量割り当てが許可されている属性
    protected $fillable = ['name', 'email', 'phone', 'affiliate_link_id'];

    // 顧客とアフィリエイトリンクのリレーション
    public function affiliateLink()
    {
        return $this->belongsTo(AffiliateLink::class);
    }
}
