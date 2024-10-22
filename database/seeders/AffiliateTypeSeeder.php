<?php

namespace Database\Seeders;

use App\Models\AffiliateType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AffiliateTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // 一般アフィリエイターをデフォルトで作成
        AffiliateType::firstOrCreate(['name' => '一般アフィリエイター']);
    }
}
