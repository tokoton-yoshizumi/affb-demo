<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'bank_account',
        'status',
    ];

    /**
     * ユーザーとのリレーション
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
