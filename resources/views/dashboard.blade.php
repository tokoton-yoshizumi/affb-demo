<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="font-bold text-2xl">アフィリエイト報酬の履歴</h3>
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full mt-4 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-md font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        日時</th>
                                    <th
                                        class="px-6 py-3 text-left text-md font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        内容</th>
                                    <th
                                        class="px-6 py-3 text-left text-md font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        金額</th>
                                    <th
                                        class="px-6 py-3 text-left text-md font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        ステータス</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @php
                                $totalCommission = 0;
                                @endphp
                                @foreach($commissions as $commission)
                                <tr class="border-b">
                                    <td class="px-6 py-4 text-md text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                        {{ $commission->created_at->format('Y年m月d日 H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-md text-gray-500 dark:text-gray-300 whitespace-nowrap">
                                        {{$commission->product_name}}
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
                                @php
                                $totalCommission += $commission->amount;
                                @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-4 font-semibold">合計報酬: ¥{{ number_format($totalCommission) }}</p>

                    <p class="mt-8">
                        あなたのアフィリエイトリンク:<br>
                        <span id="affiliateLink" class="cursor-pointer text-blue-500">
                            {{ 'https://theme.wpzen.jp/?ref=' . Auth::user()->affiliateLink->token }}
                        </span>
                        <button id="copyButton" class="ml-2 bg-blue-500 text-white px-3 py-1 rounded">コピー</button>
                        <span id="copyMessage" class="text-green-500 mt-2 hidden">コピーしました</span>
                    </p>

                    <p class="mt-2">このリンクをブログやSNSでご紹介いただき、リンクから購入された場合に報酬が発生します（{{ $rewardDescription }}）</p>

                    <!-- 振込申請ボタン -->
                    <div class="mt-6">
                        <a href="{{ route('reward.request') }}"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            報酬の振込申請はこちら
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold">あなたのアフィリエイトリンク</h3>

                    <table class="min-w-full leading-normal mt-4">
                        <thead>
                            <tr>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    商材名
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    アフィリエイトリンク
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    報酬額
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($affiliate_links as $link)
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    {{ $link->product->name }}
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    <a href="{{ $link->product->url . '?ref=' . $link->token }}" class="text-blue-500">
                                        {{ $link->product->url . '?ref=' . $link->token }}
                                    </a>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    <!-- ユーザーのaffiliate_type_idに基づいて報酬を表示 -->
                                    @php
                                    $commission = $link->product->commissions()
                                    ->where('affiliate_type_id', Auth::user()->affiliate_type_id)
                                    ->first();
                                    @endphp
                                    {{ $commission ? '¥' . number_format($commission->fixed_commission) : '報酬なし' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            var tempInput = document.createElement('input');
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);

            document.getElementById('copyMessage').classList.remove('hidden');
            setTimeout(function() {
                document.getElementById('copyMessage').classList.add('hidden');
            }, 2000);
        }

        document.getElementById('affiliateLink').addEventListener('click', function() {
            var linkText = document.getElementById('affiliateLink').textContent;
            copyToClipboard(linkText);
        });

        document.getElementById('copyButton').addEventListener('click', function() {
            var linkText = document.getElementById('affiliateLink').textContent;
            copyToClipboard(linkText);
        });
    </script>
</x-app-layout>