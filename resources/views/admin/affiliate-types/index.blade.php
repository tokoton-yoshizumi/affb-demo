<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            アフィリエイタータイプ管理
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <a href="{{ route('affiliate-types.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
                        新規作成
                    </a>

                    <table class="min-w-full mt-4">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 border-b">タイプ名</th>
                                <th class="px-6 py-3 border-b">アクション</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($affiliateTypes as $type)
                            <tr>
                                <td class="px-6 py-4 border-b">{{ $type->name }}</td>
                                <td class="px-6 py-4 border-b">
                                    <a href="{{ route('affiliate-types.edit', $type->id) }}"
                                        class="text-blue-500">編集</a>
                                    <form action="{{ route('affiliate-types.destroy', $type->id) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500">削除</button>
                                    </form>
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