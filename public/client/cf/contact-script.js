document.addEventListener("DOMContentLoaded", function () {
    function getAffiliateRef() {
        return (
            localStorage.getItem("affiliate_ref") ||
            new URLSearchParams(window.location.search).get("ref")
        );
    }

    const affiliateRef = getAffiliateRef();

    console.log("Affiliate Ref (before form submit):", affiliateRef); // デバッグ用

    // `wpcf7mailsent` を利用してフォーム送信後に処理を実行
    document.addEventListener(
        "wpcf7mailsent",
        function (event) {
            console.log("CF7送信完了イベント発火");

            const form = event.target;
            const formData = {};

            // フォームデータを取得
            form.querySelectorAll("input, textarea").forEach((input) => {
                if (input.name) {
                    formData[input.name] = input.value;
                }
            });

            // `affiliate_ref` がある場合のみ Laravel に送信
            if (affiliateRef) {
                formData["affiliate_ref"] = affiliateRef;
                formData["action"] = "CAMPFIREアフィリエイト";
                formData["product_id"] = 1;
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
                    })
                    .catch((error) => {
                        console.error("送信エラー:", error);
                    });
            } else {
                console.log(
                    "アフィリエイトリファレンスなし。Laravel への送信をスキップ。"
                );
            }

            // リダイレクト処理
            var redirectLink = document.getElementById("redirectLink");
            if (redirectLink) {
                console.log("リダイレクトを実行:", redirectLink.href);
                window.location.href = redirectLink.href; // `click()` ではなく `href` を直接設定
            } else {
                console.log("リダイレクトリンクが見つかりません。");
            }
        },
        false
    );
});
