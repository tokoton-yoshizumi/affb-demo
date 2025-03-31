<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\AffiliateLink;
use App\Models\AffiliateType;
use App\Models\RewardRequest;
use App\Models\AffiliateProgram;
use Laravel\Sanctum\HasApiTokens;
use App\Models\AffiliateCommission;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;  // Product モデルを追加

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'affiliate_program_id',
        'affiliate_type_id',   // affiliate_type_id を追加
        'bank_name',
        'branch_name',
        'account_number',
        'account_holder',
        'account_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * リレーション: ユーザーが持つアフィリエイトリンク
     */
    public function affiliateLink()
    {
        return $this->hasOne(AffiliateLink::class);
    }

    /**
     * モデルのイベントリスナーを登録します。
     */
    protected static function boot()
    {
        parent::boot();

        // ユーザーが作成された後にアフィリエイトリンクを生成し、デフォルトのaffiliate_type_idを設定
        static::created(function ($user) {


            // 全ての商品に対してアフィリエイトリンクを作成し、product_id を設定
            $products = Product::all();  // 全ての商品を取得

            if ($products->isEmpty()) {
                Log::warning('No products found for affiliate links creation', [
                    'user_id' => $user->id
                ]);
            }

            foreach ($products as $product) {
                $affiliateLink = AffiliateLink::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,  // 商品IDを設定
                    'token' => Str::random(10),    // ランダムなトークンを生成
                    'url' => $product->url . '?ref=' . $user->id,  // アフィリエイトリンクを作成
                ]);

                // 各商品に対するアフィリエイトリンクが作成されたことをログに記録
                Log::info('Affiliate link created for product', [
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'affiliate_link_id' => $affiliateLink->id,
                    'url' => $product->url . '?ref=' . $user->id
                ]);
            }

            // デフォルトのアフィリエイトタイプIDを設定（例: 一般アフィリエイターとして登録）
            $user->affiliate_type_id = 1;  // デフォルトで一般アフィリエイター
            $user->save();

            // ユーザーのaffiliate_type_idが更新されたことをログに記録
            Log::info('User affiliate_type_id set to default', [
                'user_id' => $user->id,
                'affiliate_type_id' => $user->affiliate_type_id
            ]);
        });
    }


    /**
     * リレーション: アフィリエイトプログラム
     */
    public function affiliateProgram()
    {
        return $this->belongsTo(AffiliateProgram::class);
    }

    /**
     * リレーション: アフィリエイトコミッション
     */
    public function commissions()
    {
        return $this->hasMany(AffiliateCommission::class, 'user_id');
    }

    /**
     * リレーション: アフィリエイトタイプ
     */
    public function affiliateType()
    {
        return $this->belongsTo(AffiliateType::class);
    }

    // app/Models/User.php
    public function rewardRequests()
    {
        return $this->hasMany(RewardRequest::class);
    }
}
