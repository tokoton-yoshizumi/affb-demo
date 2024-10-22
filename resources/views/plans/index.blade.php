<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('プラン一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="font-bold text-2xl">ご利用可能なプラン</h3>
                    <p class="mt-2">各プランの最低契約期間は6ヶ月とさせていただきます。</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                        @foreach($plans as $plan)
                        <div class="border border-gray-300 dark:border-gray-700 p-4 rounded-lg">
                            <h4 class="text-xl font-semibold">{{ $plan->name }}</h4>
                            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $plan->description }}</p>
                            <p class="mt-4 text-lg font-bold">
                                @if($plan->price > 0)
                                初期費用0円<br>月額 {{ number_format($plan->price) }}円
                                @else
                                お見積もり
                                @endif
                            </p>
                            @if($plan->slug === 'custom')
                            <a href="https://google.com"
                                class="mt-4 inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                お問い合わせ
                            </a>
                            @else
                            <a href="{{ route('checkout', ['plan' => $plan->slug]) }}"
                                class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                申し込む
                            </a>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>