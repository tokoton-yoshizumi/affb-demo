<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            アフィリエイタータイプを編集
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('affiliate-types.update', $affiliateType->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">タイプ名</label>
                            <input type="text" name="name" id="name" value="{{ $affiliateType->name }}"
                                class="form-input mt-1 block w-full" required>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">更新する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>