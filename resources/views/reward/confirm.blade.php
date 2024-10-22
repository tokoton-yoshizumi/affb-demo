<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('振込申請確認') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="font-bold text-2xl">振込申請確認画面</h3>

                    <p class="mt-4">申請金額: ¥{{ number_format($amount) }}</p>

                    <!-- 銀行名 -->
                    <p class="mt-4">銀行名: {{ $bank_name }}</p>

                    <!-- 支店名 -->
                    <p class="mt-4">支店名: {{ $branch_name }}</p>

                    <!-- 口座番号 -->
                    <p class="mt-4">口座番号: {{ $account_number }}</p>

                    <!-- 口座名義 -->
                    <p class="mt-4">口座名義: {{ $account_holder }}</p>

                    <!-- 口座の種類 -->
                    <p class="mt-4">口座の種類: {{ $account_type }}</p>

                    <div class="mt-6 flex items-center justify-between">
                        <div>
                            <form action="{{ route('reward.finalize') }}" method="POST">
                                @csrf
                                <input type="hidden" name="amount" value="{{ $amount }}">
                                <input type="hidden" name="bank_name" value="{{ $bank_name }}">
                                <input type="hidden" name="branch_name" value="{{ $branch_name }}">
                                <input type="hidden" name="account_number" value="{{ $account_number }}">
                                <input type="hidden" name="account_holder" value="{{ $account_holder }}">
                                <input type="hidden" name="account_type" value="{{ $account_type }}">
                                <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    申請を確定する
                                </button>
                            </form>
                            <p class="mt-2">
                                報酬申請後、口座情報に問題がなければ翌月末日にお振り込みいたします。
                            </p>
                        </div>



                        <a href="{{ route('reward.request') }}"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            戻る
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>