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
                                        種別</th>
                                    <th
                                        class="px-6 py-3 text-left text-md font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        金額</th>
                                    <th
                                        class="px-6 py-3 text-left text-md font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        ステータス</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @php $totalCommission = 0; @endphp
                                @foreach ($commissions as $commission)
                                    <tr class="border-b">
                                        <td
                                            class="px-6 py-4 text-md text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                            {{ $commission->created_at->format('Y年m月d日 H:i') }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-md text-gray-500 dark:text-gray-300 whitespace-nowrap">
                                            {{ $commission->product_name }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-md text-gray-500 dark:text-gray-300 whitespace-nowrap">
                                            {{ $commission->reward_type === 'form' ? 'フォーム送信' : '決済完了' }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-md text-gray-500 dark:text-gray-300 whitespace-nowrap">
                                            ¥{{ number_format($commission->amount) }}
                                        </td>
                                        <td class="px-6 py-4 text-md">
                                            @if ($commission->status === '確定')
                                                <span class="text-green-500 font-semibold">確定</span>
                                            @elseif($commission->created_at->diffInDays(Carbon\Carbon::now()) >= 30 && is_null($commission->paid_at))
                                                <span class="text-green-500 font-semibold">申請可</span>
                                            @elseif(!is_null($commission->paid_at))
                                                <span class="text-blue-500 font-semibold">支払済</span>
                                            @else
                                                <span class="text-gray-500 font-semibold">発生</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @php $totalCommission += $commission->amount; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-4 font-semibold">合計報酬: ¥{{ number_format($totalCommission) }}</p>

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
    <div class="pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="font-bold text-2xl">あなたのアフィリエイトリンク</h3>
                    <p class="mt-2">このリンクをブログやSNSでご紹介いただき、リンクから購入された場合に報酬が発生します</p>

                    <table class="min-w-full leading-normal mt-4">
                        <thead>
                            <tr>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-base font-semibold text-gray-600 uppercase tracking-wider">
                                    商材名</th>
                                <th
                                    class="w-[60%] px-5 py-3 border-b-2 border-gray-200 text-left text-base font-semibold text-gray-600 uppercase tracking-wider">
                                    アフィリエイトリンク</th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-base font-semibold text-gray-600 uppercase tracking-wider">
                                    報酬</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($affiliate_links as $link)
                                @if ($link->product->status === '公開')
                                    <tr class="align-top">
                                        <td class="px-5 py-5 border-b border-gray-200 text-base">
                                            {{ $link->product->name }}
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 text-base flex items-center">
                                            <span id="affiliateLink{{ $loop->index }}"
                                                class="cursor-pointer hover:underline"
                                                onclick="copyToClipboard('{{ $link->product->url . '?ref=' . $link->token }}', 'copyMessage{{ $loop->index }}'); return false;">
                                                {{ $link->product->url . '?ref=' . $link->token }}
                                            </span>
                                            <button id="copyButton{{ $loop->index }}"
                                                onclick="copyToClipboard('{{ $link->product->url . '?ref=' . $link->token }}', 'copyMessage{{ $loop->index }}')"
                                                class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 ml-2 inline-flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-5 cursor-pointer hover:underline">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
                                                </svg>
                                                <span class="text-xs">コピー</span>
                                            </button>
                                            <span id="copyMessage{{ $loop->index }}"
                                                class="ml-2 text-xs text-red-500 hidden">コピーしました！</span>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 text-base">
                                            @php
                                                $commission = $link->product
                                                    ->commissions()
                                                    ->where('affiliate_type_id', Auth::user()->affiliate_type_id)
                                                    ->first();
                                            @endphp
                                            @if ($commission)
                                                フォーム送信: ¥{{ number_format($commission->fixed_commission_on_form) }}<br>
                                                決済完了: ¥{{ number_format($commission->fixed_commission_on_payment) }}
                                            @else
                                                報酬なし
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text, messageId) {
            var tempInput = document.createElement('input');
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);

            var messageElement = document.getElementById(messageId);
            messageElement.classList.remove('hidden');
            setTimeout(function() {
                messageElement.classList.add('hidden');
            }, 2000);
        }
    </script>
</x-app-layout>
