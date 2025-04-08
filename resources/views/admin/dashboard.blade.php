<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900 dark:text-gray-100 mt-8">
                    <h3 class="text-lg font-semibold">アフィリエイト報酬一覧</h3>

                    <table class="min-w-full leading-normal mt-4">
                        <thead>
                            <tr>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    日付
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    アフィリエイター名
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    顧客名
                                </th>

                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    商材
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    種別
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    報酬
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    ステータス
                                </th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentCommissions as $commission)
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        {{ $commission->created_at->format('Y年m月d日 H:i:s') }}
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        {{ $commission->user->name }}
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        {{ $commission->customer->name ?? '不明' }}
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        {{ $commission->product_name ?? 'N/A' }}
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        {{ $commission->reward_type === 'form' ? 'フォーム送信' : '決済完了' }}

                                        @if ($commission->status === '重複')
                                            <span
                                                class="ml-2 inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">
                                                ⚠️ 重複
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        ¥{{ number_format($commission->amount) }}
                                    </td>

                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        <div x-data="{ editing: false }">
                                            @if (!$commission->status || !$commission->id)
                                                <span class="text-red-500">データ不備</span>
                                            @endif

                                            {{-- 表示モード --}}
                                            <div x-show="!editing" class="flex items-center space-x-2">
                                                @if ($commission->status === '確定')
                                                    <span
                                                        class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">確定</span>
                                                @elseif ($commission->status === '重複')
                                                    <span
                                                        class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">⚠️
                                                        重複</span>
                                                @elseif ($commission->status === '保留')
                                                    <span
                                                        class="inline-block bg-gray-200 text-gray-800 text-xs px-2 py-1 rounded">保留</span>
                                                @endif

                                                @if (auth()->user()->is_admin)
                                                    <button @click="editing = true" class="text-blue-400"><svg
                                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                            fill="currentColor" class="size-4">
                                                            <path
                                                                d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                                                            <path
                                                                d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                                                        </svg>

                                                    </button>
                                                @endif
                                            </div>

                                            {{-- 編集モード --}}
                                            @if (auth()->user()->is_admin)
                                                <form method="POST"
                                                    action="{{ route('admin.commissions.updateStatus', $commission->id) }}"
                                                    x-show="editing" x-cloak class="mt-1 inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" onchange="this.form.submit()"
                                                        class="text-xs py-1 pr-6 border rounded">
                                                        <option value="確定" @selected($commission->status === '確定')>確定</option>
                                                        <option value="重複" @selected($commission->status === '重複')>重複</option>
                                                        <option value="保留" @selected($commission->status === '保留')>保留</option>
                                                    </select>
                                                    <button type="button" @click="editing = false"
                                                        class="text-xs text-gray-500 ml-2">キャンセル</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-5 text-center border-b border-gray-200 text-sm">
                                        報酬記録はまだありません。
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $recentCommissions->links() }}
                    </div>
                </div>

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
                                        @if ($affiliate->is_partner)
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
