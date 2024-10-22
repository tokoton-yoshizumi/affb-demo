<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            商材の登録完了
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- 商材のURLを表示 -->
                    <h3 class="text-lg font-semibold">商材が登録されました。以下のコードをあなたの商材のサイト
                        (<a href="{{ $product->url }}" class="text-blue-500">{{ $product->url }}</a>) の
                        <code>&lt;/body&gt;</code>タグ直前に挿入してください。
                    </h3>

                    <!-- 背景色をつけたテキストエリア、ワンクリックでコピー -->
                    <div class="relative mt-4">
                        <textarea id="trackingCode" class="form-input bg-gray-100 mt-1 block w-full p-2 rounded"
                            rows="10" readonly>
<!-- アフィリエイトリンク用追跡コード -->
<script>
    // URLから?ref=のパラメーターを取得してクッキーに保存
    function getRefParameter() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('ref');
    }

    // refパラメーターを取得
    const ref = getRefParameter();

    // refが存在する場合、クッキーに保存
    if (ref) {
        document.cookie = "affiliate_ref=" + ref + "; path=/";
    }
</script>
                        </textarea>
                        <button onclick="copyToClipboard()"
                            class="absolute top-2 right-2 bg-blue-500 text-white px-4 py-2 rounded">コピー</button>
                    </div>

                    <p class="mt-4 text-sm">このコードは、アフィリエイトリンクを通じて訪れたユーザーを追跡するためのものです。必ず挿入してください。</p>
                    <!-- 「後で行う」と「完了」ボタン -->
                    <div class="flex justify-end mt-4">
                        <form method="POST" action="{{ route('products.track_later', ['product' => $product->id]) }}">
                            @csrf
                            <button type="submit" class="bg-gray-500 text-white px-4 py-2 rounded mr-4">後で行う</button>
                        </form>

                        <form method="POST" action="{{ route('products.track_done', ['product' => $product->id]) }}">
                            @csrf
                            <button type="submit"
                                class="bg-blue-500 text-white px-4 py-2 rounded">コードを実装したので完了する</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <script>
        // コードをクリップボードにコピーする関数
        function copyToClipboard() {
            var copyText = document.getElementById("trackingCode").value;
            navigator.clipboard.writeText(copyText).then(function() {
                alert("コードがコピーされました！");
            }, function(err) {
                console.error('コピーに失敗しました', err);
            });
        }
    </script>
</x-app-layout>