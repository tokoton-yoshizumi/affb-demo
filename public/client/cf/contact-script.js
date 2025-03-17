document.addEventListener("DOMContentLoaded", function () {
    function getAffiliateRef() {
        return (
            localStorage.getItem("affiliate_ref") ||
            new URLSearchParams(window.location.search).get("ref")
        );
    }

    const affiliateRef = getAffiliateRef();

    document.querySelectorAll("form").forEach((form) => {
        form.addEventListener("submit", function (e) {
            // CF7 のデフォルトの送信を妨げないために `e.preventDefault();` を削除

            const formData = {};
            form.querySelectorAll("input, textarea").forEach((input) => {
                if (input.name) {
                    formData[input.name] = input.value;
                }
            });

            if (affiliateRef) {
                formData["affiliate_ref"] = affiliateRef;
            }

            formData["action"] = "CAMPFIREアフィリエイト";
            formData["product_id"] = 1;
            formData["timestamp"] = new Date().toISOString();

            console.log("Sending form data to webhook:", formData);

            // Laravel にデータ送信
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
        });
    });

    // `wpcf7mailsent` を利用してリダイレクトを発火
    document.addEventListener(
        "wpcf7mailsent",
        function (event) {
            console.log("CF7送信完了イベント発火");

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
