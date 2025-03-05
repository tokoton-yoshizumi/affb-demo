<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            フォームページの設定
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- 商材のURLを表示 -->
                    <h3 class="text-lg font-semibold">商材が登録されました。<br>
                        以下のコードをあなたの商材のサイト
                        (<a href="{{ $product->url }}" class="text-blue-500">{{ $product->url }}</a>) の
                        フォームを入れたいページに挿入してください。
                    </h3>

                    <!-- 背景色をつけたテキストエリア、ワンクリックでコピー -->
                    <div class="relative mt-4">
                        <textarea id="trackingCode" class="form-input bg-gray-100 mt-1 block w-full p-2 rounded" rows="20" readonly>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // URL から `ref` を取得
        function getRefParameter() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get("ref");
        }

        const ref = getRefParameter();

        // `ref` をクッキー & localStorage に保存（30日間）
        if (ref) {
            document.cookie =
                "affiliate_ref=" +
                ref +
                "; path=/; max-age=2592000; Secure; SameSite=Lax";
            localStorage.setItem("affiliate_ref", ref);
        }

        // フォーム送信時の処理
        document.querySelectorAll("form").forEach((form) => {
            form.addEventListener("submit", function(e) {
                e.preventDefault(); // 通常のフォーム送信を防ぐ

                // フォームの送信先（決済ページ）の URL を取得
                const url = new URL(this.action); // 決済ページの URL

                // フォームデータを取得
                const formData = {};
                form.querySelectorAll("input, textarea").forEach((input) => {
                    if (input.name) {
                        formData[input.name] = input.value;
                    }
                });

                // `ref` を取得（クッキー or localStorage）
                const affiliateRef =
                    getRefParameter() || localStorage.getItem("affiliate_ref");
                if (affiliateRef) {
                    formData["affiliate_ref"] = affiliateRef;
                }

                // `fetch` で Laravel にデータを送信
                formData["action"] = "CAMPFIREアフィリエイト";
                formData["product_id"] = 1; // 商品ID（適宜変更）
                formData["timestamp"] = new Date().toISOString();

                console.log("Sending form data to webhook:", formData);

                fetch("https://demo.affb.jp/webhook/form", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify(formData),
                    })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error(
                                `HTTP error! status: ${response.status}`
                            );
                        }
                        return response.json();
                    })
                    .then((data) => {
                        console.log("データ送信成功:", data);

                        // Laravel へのデータ送信が完了したら決済ページにリダイレクト
                        Object.keys(formData).forEach((key) => {
                            url.searchParams.set(key, formData[
                            key]); // データを URL に追加
                        });
                        window.location.href = url.toString();
                    })
                    .catch((error) => {
                        console.error("送信エラー:", error);
                        alert("データ送信に失敗しました。もう一度お試しください。");
                    });
            });
        });
    });
</script>

                        </textarea>
                        <button onclick="copyToClipboard()"
                            class="absolute top-2 right-2 bg-blue-500 text-white px-4 py-2 rounded">コピー</button>
                    </div>

                    <p class="mt-4 text-sm">このコードは、アフィリエイトリンクを通じて訪れたユーザーを追跡するためのものです。必ず挿入してください。</p>

                    <!-- 「後で行う」と「完了」ボタン -->
                    <div class="flex justify-end mt-4">
                        <form method="POST" action="{{ route("products.track_later", ["product" => $product->id]) }}">
                            @csrf
                            <button type="submit" class="bg-gray-500 text-white px-4 py-2 rounded mr-4">後で行う</button>
                        </form>

                        <a href="{{ route("products.show_code_thanks", ["product" => $product->id]) }}"
                            class="bg-blue-500 text-white px-4 py-2 rounded">次へ</a>

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
                },
                function(err) {
                    console.error('コピーに失敗しました', err);
                });
        }
    </script>
</x-app-layout>
