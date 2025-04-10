document.addEventListener("DOMContentLoaded", function () {
    function getAffiliateRef() {
        return (
            localStorage.getItem("affiliate_ref") ||
            new URLSearchParams(window.location.search).get("ref")
        );
    }

    const affiliateRef = getAffiliateRef();

    console.log("Affiliate Ref (before form submit):", affiliateRef); // デバッグ用

    // hiddenフィールドにaffiliate_refを埋め込む（フォーム送信前）
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

    // 送信完了時のデバッグ用ログ（fetchは行わない）
    document.addEventListener("wpcf7mailsent", function () {
        console.log(
            "CF7送信完了イベント発火（fetch送信はfunctions.phpに移行済）"
        );
    });
});
