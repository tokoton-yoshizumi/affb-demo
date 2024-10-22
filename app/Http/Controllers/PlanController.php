<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        // すべてのプランを取得してビューに渡す
        $plans = Plan::all();
        return view('plans.index', compact('plans'));
    }
}
