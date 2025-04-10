<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('振込申請一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold">振込申請一覧</h3>

                    <table class="min-w-full leading-normal mt-4">
                        <thead>
                            <tr>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left font-semibold text-gray-600 uppercase tracking-wider">
                                    ユーザー名
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left font-semibold text-gray-600 uppercase tracking-wider">
                                    申請金額
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left font-semibold text-gray-600 uppercase tracking-wider">
                                    ステータス / アクション
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left font-semibold text-gray-600 uppercase tracking-wider">
                                    申請日時
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left font-semibold text-gray-600 uppercase tracking-wider">
                                    振込先口座情報
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rewardRequests as $request)
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200">{{ $request->user->name }}</td>
                                    <td class="px-5 py-5 border-b border-gray-200">
                                        ¥{{ number_format($request->amount) }}
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200">
                                        <!-- ステータス変更フォーム -->
                                        <form action="{{ route('admin.reward-requests.update', $request->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')

                                            <select name="status" class="border rounded px- py-1">
                                                <option value="pending"
                                                    {{ $request->status === 'pending' ? 'selected' : '' }}>申請受付</option>
                                                <option value="approved"
                                                    {{ $request->status === 'approved' ? 'selected' : '' }}>支払済</option>
                                                <option value="rejected"
                                                    {{ $request->status === 'rejected' ? 'selected' : '' }}>申請拒否
                                                </option>
                                            </select>
                                            <button type="submit"
                                                class="ml-2 px-4 py-1 bg-blue-500 text-white rounded">更新</button>
                                        </form>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200">
                                        {{ $request->created_at->format('Y-m-d H:i') }}
                                    </td>
                                    <!-- 振込先口座情報 -->
                                    <td class="px-5 py-5 border-b border-gray-200">
                                        {{ $request->user->bank_name }}<br>
                                        {{ $request->user->branch_name }}<br>
                                        {{ $request->user->account_number }}<br>
                                        {{ $request->user->account_type }}<br>
                                        {{ $request->user->account_holder }}
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
