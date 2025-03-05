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
                    <!-- 商材のURLを表示 -->
                    <h3 class="text-lg font-semibold">
                        以下のコードをあなたの商材
                        (<a href="{{ $product->url }}" class="text-blue-500">{{ $product->url }}</a>)
                        のフォーム送信の完了ページ（サンクスページ）に挿入してください。
                    </h3>


                    <!-- 背景色をつけたテキストエリア、ワンクリックでコピー -->
                    <div class="relative mt-4">
                        <textarea id="trackingCode" class="form-input bg-gray-100 mt-1 block w-full p-2 rounded"
                            rows="20" readonly>
<script>
    // クッキーからaffiliate_refを取得
  function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(";").shift();
  }

  const affiliateRef = getCookie("affiliate_ref");

  // URLパラメータから全てのデータを取得
  const params = new URLSearchParams(window.location.search);
  const data = {};

  params.forEach((value, key) => {
    data[key] = value; // URLパラメータのキーと値をそのままオブジェクトに保存
  });

const productId = @json($product->id); // サーバーサイドから商品IDを取得

  console.log("Sending data to webhook...");
  console.log("affiliateRef:", affiliateRef);
  console.log("Form data:", data);

  if (affiliateRef && data.name && data.email) {
    // アフィリエイトシステムにデータを送信
    fetch("{{ config('services.webhook_url') }}", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        affiliate_ref: affiliateRef,
        action: "資料請求",
        product_id: productId, // 商品IDを送信
        ...data, // 他のデータをそのまま送信
        timestamp: new Date().toISOString(),
      }),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then((data) => {
        console.log("トラッキングデータが送信されました:", data);
      })
      .catch((error) => console.error("エラー:", error));
  }
</script>
                        </textarea>
                        <button onclick="copyToClipboard()"
                            class="absolute top-2 right-2 bg-blue-500 text-white px-4 py-2 rounded">コピー</button>
                    </div>

                    <div class="flex justify-between">

                        <!-- 戻るボタン -->
                        <div class="flex justify-start mt-4">
                            <a href="{{ route('products.showCode', ['product' => $product->id]) }}"
                                class="bg-gray-300 text-black px-4 py-2 rounded">
                                戻る
                            </a>
                        </div>
                        <!-- 「後で行う」と「完了」ボタン -->
                        <div class="flex justify-end mt-4">
                            <form method="POST"
                                action="{{ route('products.track_later', ['product' => $product->id]) }}">
                                @csrf
                                <button type="submit"
                                    class="bg-gray-500 text-white px-4 py-2 rounded mr-4">後で行う</button>
                            </form>

                            <form method="POST"
                                action="{{ route('products.track_done', ['product' => $product->id]) }}">
                                @csrf
                                <button type="submit"
                                    class="bg-blue-500 text-white px-4 py-2 rounded">コードを実装したので完了する</button>
                            </form>
                        </div>
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
