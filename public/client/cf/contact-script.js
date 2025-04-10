document.addEventListener("DOMContentLoaded", function () {
    function getAffiliateRef() {
        return (
            localStorage.getItem("affiliate_ref") ||
            new URLSearchParams(window.location.search).get("ref")
        );
    }

    const affiliateRef = getAffiliateRef();

    // デバッグ情報表示用の要素を追加
    const debugBox = document.createElement("div");
    debugBox.style.position = "fixed";
    debugBox.style.bottom = "0";
    debugBox.style.left = "0";
    debugBox.style.right = "0";
    debugBox.style.backgroundColor = "rgba(0, 0, 0, 0.8)";
    debugBox.style.color = "#fff";
    debugBox.style.padding = "10px";
    debugBox.style.fontSize = "14px";
    debugBox.style.zIndex = "9999";
    debugBox.style.textAlign = "center";
    debugBox.innerText = "affiliate_ref: " + (affiliateRef ?? "なし");
    document.body.appendChild(debugBox);

    console.log("Affiliate Ref (before form submit):", affiliateRef);

    // hiddenフィールドにaffiliate_refを埋め込む
    if (affiliateRef) {
        const hiddenInput = document.querySelector(
            'input[name="affiliate_ref"]'
        );
        if (hiddenInput) {
            hiddenInput.value = affiliateRef;
            console.log("affiliate_ref hiddenにセット完了:", affiliateRef);
        } else {
            console.warn(
                "hiddenフィールド affiliate_ref が見つかりませんでした"
            );
        }
    }

    document.addEventListener("wpcf7mailsent", function () {
        console.log(
            "CF7送信完了イベント発火（fetch送信はfunctions.phpに移行済）"
        );
    });
});
