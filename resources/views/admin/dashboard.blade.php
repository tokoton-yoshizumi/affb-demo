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
                    <h3 class="text-lg font-semibold">アフィリエイター一覧</h3>

                    <table class="min-w-full leading-normal mt-4">
                        <thead>
                            <tr>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    名前
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    メールアドレス
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    登録日
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    ステータス
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    合計報酬
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($affiliates as $affiliate)
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    {{ $affiliate->name }}
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    {{ $affiliate->email }}
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    {{ $affiliate->created_at->format('Y-m-d') }}
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    @if($affiliate->is_partner)
                                    ZENサポーター
                                    @elseif($affiliate->is_agent)
                                    特別代理店
                                    @else
                                    一般アフィリエイター
                                    @endif
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    ¥{{ number_format($affiliate->commissions->sum('amount')) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>