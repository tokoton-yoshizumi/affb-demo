<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        // 顧客情報を表示
        $customers = Customer::all();
        return view('admin.customers.index', compact('customers'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);

        // 顧客の関連するsubmissionデータを取得
        $submissions = $customer->submissions;

        // 顧客情報とsubmissionをビューに渡す
        return view('customers.edit', compact('customer', 'submissions'));
    }


    public function show($id)
    {
        // 顧客情報詳細表示
        $customer = Customer::findOrFail($id);
        return view('admin.customers.show', compact('customer'));
    }

    public function destroy($id)
    {
        // 顧客削除
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', '顧客が削除されました');
    }
}
