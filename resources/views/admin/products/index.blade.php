<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __("商材一覧") }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between">
                        <h3 class="text-2xl font-semibold">商材一覧</h3>

                        <!-- 新規登録ボタン -->
                        <div class="my-4">
                            <a href="{{ route("products.create") }}" class="bg-blue-500 text-white px-4 py-2 rounded">
                                新規登録
                            </a>
                        </div>
                    </div>

                    <table class="min-w-full leading-normal mt-4">
                        <thead>
                            <tr>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left  font-semibold text-gray-600 uppercase tracking-wider">
                                    商材名
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left  font-semibold text-gray-600 uppercase tracking-wider">
                                    説明
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left  font-semibold text-gray-600 uppercase tracking-wider">
                                    アフィリエイターへの報酬
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 text-left  font-semibold text-gray-600 uppercase tracking-wider">
                                    URL
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200">
                                        <a class="hover:underline hover:text-blue-600"
                                            href="{{ route("products.edit", $product->id) }}">{{ $product->name }}</a>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200">
                                        {{ $product->description }}
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200">
                                        <form action="{{ route("products.update", $product->id) }}" method="POST">
                                            @csrf
                                            @method("PUT")
                                            <select name="status" onchange="this.form.submit()"
                                                class="border rounded px-2 py-1">
                                                <option value="公開" {{ $product->status == "公開" ? "selected" : "" }}>
                                                    公開</option>
                                                <option value="非公開"
                                                    {{ $product->status == "非公開" ? "selected" : "" }}>非公開</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
