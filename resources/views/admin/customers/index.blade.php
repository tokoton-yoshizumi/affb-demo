<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            顧客管理
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between">
                        <h3 class="text-2xl font-semibold">顧客一覧</h3>
                        <div class="my-4">
                            {{-- <a href="{{ route('customers.create') }}"
                                class="bg-blue-500 text-white px-4 py-2 rounded">
                                新規登録
                            </a> --}}
                        </div>
                    </div>

                    <table class="min-w-full leading-normal mt-4">
                        <thead>
                            <tr>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left font-semibold text-gray-600 uppercase tracking-wider">
                                    名前</th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left font-semibold text-gray-600 uppercase tracking-wider">
                                    メールアドレス</th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left font-semibold text-gray-600 uppercase tracking-wider">
                                    電話番号</th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left font-semibold text-gray-600 uppercase tracking-wider">
                                    登録日時</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $customer)
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200">
                                    <a href="{{ route('customers.edit', $customer->id) }}"
                                        class="text-blue-500 hover:underline">
                                        {{ $customer->name }}
                                    </a>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200">{{ $customer->email }}</td>
                                <td class="px-5 py-5 border-b border-gray-200">{{ $customer->phone ?? '未設定' }}</td>
                                <td class="px-5 py-5 border-b border-gray-200">{{ $customer->created_at->format('Y年m月d日
                                    H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
