<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Plan::create([
            'name' => 'ベーシックプラン',
            'slug' => 'basic',
            'price' => 9800, // 月額9,800円
            'description' => '月額9,800円で、基本的なアフィリエイト機能をご利用いただけるプランです。',
        ]);

        Plan::create([
            'name' => 'プレミアムプラン',
            'slug' => 'premium',
            'price' => 19800, // 月額19,800円
            'description' => '月額19,800円で、より高度なアフィリエイト機能とサポートを提供するプランです。',
        ]);

        Plan::create([
            'name' => 'オーダーメイドプラン',
            'slug' => 'custom',
            'price' => 0, // 価格はお見積もり
            'description' => 'お客様のニーズに合わせたカスタマイズプランです。詳細はお問い合わせください。',
        ]);
    }
}
