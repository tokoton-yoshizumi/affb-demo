<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $affiliate->name }} さんの詳細
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                @if (session('success'))
                    <div class="p-6">
                        <div
                            class="rounded-md bg-green-50 p-4 border border-green-200 dark:bg-green-900/10 dark:border-green-700">
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    <svg class="size-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800 dark:text-green-100">
                                        {{ session('success') }}
                                    </p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <button type="button"
                                        class="inline-flex rounded-md bg-green-50 p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 focus:ring-offset-green-50"
                                        onclick="this.closest('div.rounded-md').remove();">
                                        <span class="sr-only">閉じる</span>
                                        <svg class="size-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">基本情報</h3>
                    <div class="space-y-2 text-sm">
                        <p><strong>メールアドレス：</strong>{{ $affiliate->email }}</p>
                        <p><strong>ステータス：</strong>
                            @if ($affiliate->is_partner)
                                ZENサポーター
                            @elseif ($affiliate->is_agent)
                                特別代理店
                            @else
                                一般アフィリエイター
                            @endif
                        </p>
                        <p><strong>合計報酬：</strong>¥{{ number_format($affiliate->commissions->sum('amount')) }}</p>
                    </div>

                </div>

                <div class="p-6 text-gray-900 dark:text-gray-100 mt-8">
                    <h3 class="text-lg font-semibold">アフィリエイトリンク一覧</h3>

                    <table class="min-w-full leading-normal mt-4">
                        <thead>
                            <tr>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    商材</th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    トークン</th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    ステータス</th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($affiliate->affiliateLinks as $link)
                                <tr
                                    class="{{ !$link->is_active ? 'bg-gray-100 dark:bg-gray-700/30 text-gray-500' : '' }}">
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        {{ $link->product->name ?? '不明' }}
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        {!! $link->is_active ? e($link->token) : '<del>' . e($link->token) . '</del>' !!}
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        @if ($link->is_active)
                                            <span class="inline-flex items-center text-green-600">
                                                <svg class="size-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 0 1 0 1.414L8.414 15 4.293 10.879a1 1 0 0 1 1.414-1.414L8.414 12.586l7.293-7.293a1 1 0 0 1 1.414 0Z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                有効
                                            </span>
                                        @else
                                            <span class="inline-flex items-center text-gray-500">
                                                <svg class="size-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M4.293 4.293a1 1 0 0 1 1.414 0L10 8.586l4.293-4.293a1 1 0 1 1 1.414 1.414L11.414 10l4.293 4.293a1 1 0 0 1-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 0 1-1.414-1.414L8.586 10 4.293 5.707a1 1 0 0 1 0-1.414Z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                無効
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        <form method="POST"
                                            action="{{ route('admin.affiliateLinks.toggle', $link->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="text-blue-500 underline text-sm"
                                                onclick="return confirm('本当に変更しますか？')">
                                                {{ $link->is_active ? '無効化' : '有効化' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-5 text-center border-b border-gray-200 text-sm">
                                        リンクはまだ登録されていません。
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>

                    <div class="mt-4">
                        <form method="POST" action="{{ route('admin.affiliates.disableAllLinks', $affiliate->id) }}">
                            @csrf
                            @method('PATCH')
                            <button class="px-4 py-2 bg-yellow-500 text-white rounded"
                                onclick="return confirm('すべてのリンクを無効化しますか？')">
                                すべてのリンクを無効化
                            </button>
                        </form>
                    </div>

                </div>

                <div class="p-6 text-gray-900 dark:text-gray-100 mt-8">
                    <h3 class="text-lg font-semibold">報酬履歴</h3>

                    <table class="min-w-full leading-normal mt-4">
                        <thead>
                            <tr>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    日付</th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    種別</th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    報酬</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($affiliate->commissions as $commission)
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        {{ $commission->created_at->format('Y年m月d日 H:i') }}
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        {{ $commission->reward_type === 'form' ? 'フォーム送信' : '決済完了' }}
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        ¥{{ number_format($commission->amount) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-5 text-center border-b border-gray-200 text-sm">
                                        報酬記録はまだありません。
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
            <div
                class="p-6 mt-10 text-gray-900 dark:text-gray-100 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-700 rounded">
                <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-2">アフィリエイターの削除</h3>
                <p class="text-sm mb-4">この操作は元に戻せません。アフィリエイターとそのリンク、報酬データが完全に削除されます。</p>

                <form method="POST" action="{{ route('admin.affiliates.destroy', $affiliate->id) }}">
                    @csrf
                    @method('DELETE')
                    <button class="px-4 py-2 bg-red-600 text-white rounded" onclick="return confirm('本当に削除しますか？')">
                        アフィリエイターを削除する
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
