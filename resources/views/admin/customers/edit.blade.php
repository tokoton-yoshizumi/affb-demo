<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            顧客の編集・詳細
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('customers.update', $customer->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">名前</label>
                            <input type="text" name="name" id="name" value="{{ $customer->name }}"
                                class="form-input mt-1 block w-full" required>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">メールアドレス</label>
                            <input type="email" name="email" id="email" value="{{ $customer->email }}"
                                class="form-input mt-1 block w-full" required>
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-gray-700">電話番号</label>
                            <input type="text" name="phone" id="phone" value="{{ $customer->phone }}"
                                class="form-input mt-1 block w-full">
                        </div>

                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700">住所</label>
                            <input type="text" name="address" id="address" value="{{ $customer->address }}"
                                class="form-input mt-1 block w-full">
                        </div>

                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">更新</button>
                    </form>

                    <h3 class="text-xl font-semibold mt-6">履歴</h3>
                    <table class="min-w-full leading-normal mt-4">
                        <thead>
                            <tr>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left font-semibold text-gray-600 uppercase tracking-wider">
                                    商材</th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left font-semibold text-gray-600 uppercase tracking-wider">
                                    アクション</th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left font-semibold text-gray-600 uppercase tracking-wider">
                                    日時</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($submissions as $submission)
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200">{{ $submission->product->name ?? '商品なし'
                                    }}</td>
                                <td class="px-5 py-5 border-b border-gray-200">{{ $submission->action }}</td>
                                <td class="px-5 py-5 border-b border-gray-200">{{ $submission->created_at->format('Y-m-d
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