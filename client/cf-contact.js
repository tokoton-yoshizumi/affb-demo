document.addEventListener("DOMContentLoaded", function () {
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
        form.addEventListener("submit", function (e) {
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
                        url.searchParams.set(key, formData[key]); // データを URL に追加
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
