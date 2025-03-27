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

                        <!-- 商材名 -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">商材名</label>
                            <input type="text" name="name" id="name" value="{{ $product->name }}"
                                class="form-input mt-1 block w-full rounded-md border-gray-300" required>
                        </div>

                        <!-- 説明 -->
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">説明</label>
                            <textarea name="description" id="description" class="form-input mt-1 block w-full rounded-md border-gray-300" required>{{ $product->description }}</textarea>
                        </div>

                        <!-- 価格 -->
                        <div class="mb-4">
                            <label for="price" class="block text-sm font-medium text-gray-700">価格</label>
                            <input type="number" name="price" id="price" value="{{ $product->price }}"
                                class="form-input mt-1 block w-full rounded-md border-gray-300" required>
                        </div>

                        <!-- 商材URL -->
                        <div class="mb-4">
                            <label for="url" class="block text-sm font-medium text-gray-700">商材URL</label>
                            <input type="url" name="url" id="url" value="{{ $product->url }}"
                                class="form-input mt-1 block w-full rounded-md border-gray-300" required>
                        </div>

                        <!-- サンクスページのURL -->
                        <div class="mb-4">
                            <label for="thank_you_url"
                                class="block text-sm font-medium text-gray-700">サンクスページのURL</label>
                            <input type="url" name="thank_you_url" id="thank_you_url"
                                class="form-input mt-1 block w-full rounded-md border-gray-300"
                                placeholder="https://example.com/thank-you">
                        </div>

                        {{-- <!-- 決済の有無 -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">決済の有無</label>
                            <div class="flex items-center space-x-4">
                                <label>
                                    <input type="radio" name="payment_option" value="none"
                                        {{ $product->price_id || $product->robot_price_id ? '' : 'checked' }}
                                        onclick="togglePaymentOption()">
                                    決済なし
                                </label>
                                <label>
                                    <input type="radio" name="payment_option" value="with_payment"
                                        {{ $product->price_id || $product->robot_price_id ? 'checked' : '' }}
                                        onclick="togglePaymentOption()">
                                    決済あり
                                </label>
                            </div>
                        </div>

                        <!-- 決済プラットフォーム選択 -->
                        <div id="payment-options"
                            class="mb-4 {{ $product->price_id || $product->robot_price_id ? '' : 'hidden' }}">
                            <label class="block text-sm font-medium text-gray-700">決済プラットフォーム</label>
                            <div class="flex items-center space-x-4">
                                <label>
                                    <input type="radio" name="payment_platform" value="stripe"
                                        {{ $product->price_id ? 'checked' : '' }} onclick="togglePlatform()">
                                    Stripe
                                </label>
                                <label>
                                    <input type="radio" name="payment_platform" value="robot"
                                        {{ $product->robot_price_id ? 'checked' : '' }} onclick="togglePlatform()">
                                    Robot Payment
                                </label>
                            </div>
                        </div>

                        <!-- Stripe 価格ID -->
                        <div id="stripe-price-id" class="mb-4 {{ $product->price_id ? '' : 'hidden' }}">
                            <label for="price_id" class="block text-sm font-medium text-gray-700">Stripe 価格ID</label>
                            <input type="text" name="price_id" id="price_id"
                                class="form-input mt-1 block w-full rounded-md border-gray-300"
                                placeholder="price_xxxxx" value="{{ old('price_id', $product->price_id) }}">
                        </div>

                        <!-- Robot Payment 価格ID -->
                        <div id="robot-price-id" class="mb-4 {{ $product->robot_price_id ? '' : 'hidden' }}">
                            <label for="robot_price_id" class="block text-sm font-medium text-gray-700">Robot Payment
                                価格ID</label>
                            <input type="text" name="robot_price_id" id="robot_price_id"
                                class="form-input mt-1 block w-full rounded-md border-gray-300"
                                placeholder="robot_xxxxx"
                                value="{{ old('robot_price_id', $product->robot_price_id) }}">
                        </div> --}}

                        <!-- アフィリエイタータイプごとの報酬 -->
                        @foreach ($affiliateTypes as $type)
                            <div
                                class="mb-6 border border-gray-300 dark:border-gray-600 p-5 rounded-xl bg-gray-50 dark:bg-gray-700 shadow-sm">
                                <h4 class="font-semibold text-base text-gray-800 dark:text-gray-100 mb-4 border-b pb-2">
                                    {{ $type->name }} の報酬設定
                                </h4>

                                <!-- フォーム送信報酬 -->
                                <div class="mb-4">
                                    <label for="commissions_form_{{ $type->id }}"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        フォーム送信報酬（円）
                                    </label>
                                    <input type="number" name="commissions_form[{{ $type->id }}]"
                                        id="commissions_form_{{ $type->id }}"
                                        class="form-input mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                        value="{{ old('commissions_form.' . $type->id, $formCommissions[$type->id] ?? '') }}"
                                        placeholder="例: 500">
                                </div>

                                <!-- 決済完了報酬 -->
                                <div>
                                    <label for="commissions_payment_{{ $type->id }}"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        決済完了報酬（円）
                                    </label>
                                    <input type="number" name="commissions_payment[{{ $type->id }}]"
                                        id="commissions_payment_{{ $type->id }}"
                                        class="form-input mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                        value="{{ old('commissions_payment.' . $type->id, $paymentCommissions[$type->id] ?? '') }}"
                                        placeholder="例: 3000">
                                </div>
                            </div>
                        @endforeach

                        <!-- 商材のステータス -->
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">表示ステータス</label>
                            <select name="status" id="status"
                                class="form-input mt-1 block w-full rounded-md border-gray-300">
                                <option value="公開" {{ $product->status == '公開' ? 'selected' : '' }}>公開</option>
                                <option value="非公開" {{ $product->status == '非公開' ? 'selected' : '' }}>非公開</option>
                            </select>
                        </div>

                        <!-- 保存ボタン -->
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">保存</button>
                    </form>

                    <!-- 削除ボタン -->
                    <form method="POST" action="{{ route('products.destroy', $product->id) }}" class="mt-4">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded"
                            onclick="return confirm('本当に削除しますか？この操作は元に戻せません。')">
                            この商品を削除
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- <!-- JavaScript -->
    <script>
        function togglePaymentOption() {
            const paymentOption = document.querySelector('input[name="payment_option"]:checked').value;
            const paymentOptionsDiv = document.getElementById('payment-options');
            const stripePriceIdDiv = document.getElementById('stripe-price-id');
            const robotPriceIdDiv = document.getElementById('robot-price-id');

            if (paymentOption === 'with_payment') {
                paymentOptionsDiv.classList.remove('hidden');
            } else {
                paymentOptionsDiv.classList.add('hidden');
                stripePriceIdDiv.classList.add('hidden');
                robotPriceIdDiv.classList.add('hidden');
            }
        }

        function togglePlatform() {
            const platform = document.querySelector('input[name="payment_platform"]:checked').value;
            const stripePriceIdDiv = document.getElementById('stripe-price-id');
            const robotPriceIdDiv = document.getElementById('robot-price-id');

            if (platform === 'stripe') {
                stripePriceIdDiv.classList.remove('hidden');
                robotPriceIdDiv.classList.add('hidden');
            } else if (platform === 'robot') {
                stripePriceIdDiv.classList.add('hidden');
                robotPriceIdDiv.classList.remove('hidden');
            }
        }
    </script> --}}
</x-app-layout>
