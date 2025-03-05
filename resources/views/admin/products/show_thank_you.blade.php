<!-- resources/views/admin/products/show_thank_you.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            サンクスページの設定
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-semibold">サンクスページのコード</h3>
                    <textarea class="form-input mt-1 block w-full" rows="10" readonly>
<script>
    // サンクスページのスクリプト
    // 必要なコードをここに記述
</script>
                    </textarea>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
