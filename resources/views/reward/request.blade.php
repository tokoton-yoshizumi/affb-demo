<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('振込申請') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="font-bold text-2xl">振込申請フォーム</h3>

                    @if(session('success'))
                    <div class="mt-4 p-4 bg-green-500 text-white rounded">
                        {{ session('success') }}
                    </div>
                    @endif

                    <p class="mt-4">現在の合計報酬: ¥{{ number_format($totalCommission) }}（発生から30日経過した報酬）</p>
                    <p>報酬申請後、口座情報に問題がなければ翌月末日にお振り込みいたします。
                    </p>


                    <!-- 申請可能な報酬がある場合のみ表示 -->
                    @if($totalCommission > 0)
                    <p class="mt-4 text-gray-600 dark:text-gray-400">※全額が申請されます</p>

                    <form action="{{ route('reward.confirm') }}" method="POST" class="mt-6">
                        @csrf

                        <!-- 銀行名 -->
                        <div class="mb-4">
                            <label for="bank_name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">銀行名</label>
                            <input type="text" name="bank_name" id="bank_name"
                                class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                value="{{ old('bank_name', $bankDetails['bank_name'] ?? '') }}" required>
                        </div>

                        <!-- 支店名 -->
                        <div class="mb-4">
                            <label for="branch_name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">支店名</label>
                            <input type="text" name="branch_name" id="branch_name"
                                class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                value="{{ old('branch_name', $bankDetails['branch_name'] ?? '') }}" required>
                        </div>

                        <!-- 口座の種類 -->
                        <div class="mb-4">
                            <label for="account_type"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">口座の種類</label>
                            <select name="account_type" id="account_type"
                                class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required>
                                <option value="普通預金" {{ old('account_type', $bankDetails['account_type'] ?? '' )=='普通預金'
                                    ? 'selected' : '' }}>
                                    普通預金</option>
                                <option value="当座預金" {{ old('account_type', $bankDetails['account_type'] ?? '' )=='当座預金'
                                    ? 'selected' : '' }}>
                                    当座預金</option>
                            </select>
                        </div>

                        <!-- 口座番号 -->
                        <div class="mb-4">
                            <label for="account_number"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">口座番号</label>
                            <input type="text" name="account_number" id="account_number"
                                class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                value="{{ old('account_number', $bankDetails['account_number'] ?? '') }}" required>
                        </div>

                        <!-- 口座名義 -->
                        <div class="mb-4">
                            <label for="account_holder"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">口座名義</label>
                            <input type="text" name="account_holder" id="account_holder"
                                class="mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                value="{{ old('account_holder', $bankDetails['account_holder'] ?? '') }}" required>
                        </div>



                        <!-- 申請ボタン -->
                        <button type="submit"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            振込申請の確認へ進む
                        </button>
                    </form>
                    @else
                    <p class="mt-4 text-red-500">現在、申請可能な報酬はありません。報酬発生から30日が経過すると申請可能になります。</p>
                    @endif

                    <!-- 過去の報酬一覧 -->
                    <h4 class="font-bold text-xl mt-20">報酬ステータス</h4>
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full mt-4 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-md font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        日付</th>
                                    <th
                                        class="px-6 py-3 text-left text-md font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        金額</th>
                                    <th
                                        class="px-6 py-3 text-left text-md font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        ステータス</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($commissions as $commission)
                                <tr class="border-b">
                                    <td class="px-6 py-4 text-md text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                        {{ $commission->created_at->format('Y年m月d日 H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-md text-gray-500 dark:text-gray-300 whitespace-nowrap">
                                        ¥{{ number_format($commission->amount) }}
                                    </td>
                                    <td class="px-6 py-4 text-md">
                                        @if($commission->created_at->diffInDays(Carbon\Carbon::now()) >= 30 &&
                                        is_null($commission->paid_at))
                                        <span class="text-green-500 font-semibold">申請可</span>
                                        @elseif(!is_null($commission->paid_at))
                                        <span class="text-blue-500 font-semibold">支払済</span>
                                        @else
                                        <span class="text-gray-500 font-semibold">発生</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>