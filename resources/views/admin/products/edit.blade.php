<!-- resources/views/admin/products/edit.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            商材の編集
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('products.update', $product->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">商材名</label>
                            <input type="text" name="name" id="name" value="{{ $product->name }}"
                                class="form-input mt-1 block w-full" required>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">説明</label>
                            <textarea name="description" id="description" class="form-input mt-1 block w-full"
                                required>{{ $product->description }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="price" class="block text-sm font-medium text-gray-700">価格</label>
                            <input type="number" name="price" id="price" value="{{ $product->price }}"
                                class="form-input mt-1 block w-full" required>
                        </div>

                        <div class="mb-4">
                            <label for="url" class="block text-sm font-medium text-gray-700">商材URL</label>
                            <input type="url" name="url" id="url" value="{{ $product->url }}"
                                class="form-input mt-1 block w-full" required>
                        </div>

                        <div class="mb-4">
                            <label for="price_id" class="block text-sm font-medium text-gray-700">Stripe 価格ID</label>
                            <input type="text" name="price_id" id="price_id" class="form-input mt-1 block w-full"
                                placeholder="price_xxxxx" required>
                        </div>

                        <!-- アフィリエイタータイプごとの報酬入力フィールド -->
                        @foreach($affiliateTypes as $type)
                        <div class="mb-4">
                            <label for="commission_{{ $type->id }}" class="block text-sm font-medium text-gray-700">
                                {{ $type->name }}の報酬（固定）
                            </label>
                            <input type="number" name="commissions[{{ $type->id }}]" id="commission_{{ $type->id }}"
                                value="{{ $commissions[$type->id] ?? '' }}" class="form-input mt-1 block w-full">
                        </div>
                        @endforeach

                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">次へ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>