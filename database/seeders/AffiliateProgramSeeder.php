<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AffiliateProgram;

class AffiliateProgramSeeder extends Seeder
{
    public function run()
    {
        AffiliateProgram::create([
            'name' => '公認サポータープログラム',
            'commission_rate' => 20.0, // 20%の報酬率
        ]);
    }
}
