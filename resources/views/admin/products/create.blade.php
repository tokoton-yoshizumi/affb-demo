<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            商材の登録
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route("products.store") }}">
                        @csrf

                        <!-- 商材名 -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">商材名</label>
                            <input type="text" name="name" id="name" class="form-input mt-1 block w-full"
                                required>
                        </div>

                        <!-- 説明 -->
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">説明</label>
                            <textarea name="description" id="description" class="form-input mt-1 block w-full"></textarea>
                        </div>

                        <!-- 価格 -->
                        <div class="mb-4">
                            <label for="price" class="block text-sm font-medium text-gray-700">価格</label>
                            <input type="number" name="price" id="price" class="form-input mt-1 block w-full">
                        </div>

                        <!-- 商材URL -->
                        <div class="mb-4">
                            <label for="url" class="block text-sm font-medium text-gray-700">商材URL</label>
                            <input type="url" name="url" id="url" class="form-input mt-1 block w-full"
                                placeholder="https://example.com" required>
                        </div>

                        <!-- サンクスページのURL -->
                        <div class="mb-4">
                            <label for="thank_you_url"
                                class="block text-sm font-medium text-gray-700">サンクスページのURL</label>
                            <input type="url" name="thank_you_url" id="thank_you_url"
                                class="form-input mt-1 block w-full" placeholder="https://example.com/thank-you">
                        </div>

                        <!-- 決済有無 -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">決済の有無</label>
                            <div class="flex items-center space-x-4">
                                <label>
                                    <input type="radio" name="payment_option" value="none" checked
                                        onclick="togglePaymentOption()" />
                                    決済なし
                                </label>
                                <label>
                                    <input type="radio" name="payment_option" value="with_payment"
                                        onclick="togglePaymentOption()" />
                                    決済あり
                                </label>
                            </div>
                        </div>

                        <!-- 決済プラットフォーム選択 -->
                        <div id="payment-options" class="mb-4 hidden">
                            <label class="block text-sm font-medium text-gray-700">決済プラットフォーム</label>
                            <div class="flex items-center space-x-4">
                                <label>
                                    <input type="radio" name="payment_platform" value="stripe"
                                        onclick="togglePlatform()" />
                                    Stripe
                                </label>
                                <label>
                                    <input type="radio" name="payment_platform" value="robot"
                                        onclick="togglePlatform()" />
                                    Robot Payment
                                </label>
                            </div>
                        </div>

                        <!-- Stripe 価格ID -->
                        <div id="stripe-price-id" class="mb-4 hidden">
                            <label for="price_id" class="block text-sm font-medium text-gray-700">Stripe 価格ID</label>
                            <input type="text" name="price_id" id="price_id" class="form-input mt-1 block w-full"
                                placeholder="price_xxxxx">
                        </div>

                        <!-- Robot Payment 価格ID -->
                        <div id="robot-price-id" class="mb-4 hidden">
                            <label for="robot_price_id" class="block text-sm font-medium text-gray-700">Robot Payment
                                価格ID</label>
                            <input type="text" name="robot_price_id" id="robot_price_id"
                                class="form-input mt-1 block w-full" placeholder="robot_xxxxx">
                        </div>

                        <!-- アフィリエイタータイプごとの報酬 -->
                        @foreach ($affiliateTypes as $type)
                            <div class="mb-4">
                                <label for="commission_{{ $type->id }}"
                                    class="block text-sm font-medium text-gray-700">
                                    {{ $type->name }}の報酬（固定）
                                </label>
                                <input type="number" name="commissions[{{ $type->id }}]"
                                    id="commission_{{ $type->id }}" class="form-input mt-1 block w-full">
                            </div>
                        @endforeach

                        <!-- ステータス（公開 or 非公開） -->
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">表示ステータス</label>
                            <select name="status" id="status" class="form-input mt-1 block w-full">
                                <option value="公開" selected>公開</option>
                                <option value="非公開">非公開</option>
                            </select>
                        </div>

                        <!-- 登録ボタン -->
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">次へ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
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
    </script>
</x-app-layout>
