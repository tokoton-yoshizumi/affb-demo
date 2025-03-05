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

    // `お問い合わせ` ボタンをクリックしたときに `ref` を `contact` に渡す
    document
        .querySelector(".contact-button")
        .addEventListener("click", function (event) {
            event.preventDefault(); // デフォルトの動作を防ぐ

            let contactUrl = this.getAttribute("href"); // 元のリンク先
            const storedRef = localStorage.getItem("affiliate_ref");

            // `ref` がある場合は URL に追加
            if (storedRef) {
                contactUrl +=
                    (contactUrl.includes("?") ? "&" : "?") + "ref=" + storedRef;
            }

            window.location.href = contactUrl; // `contact` へ遷移
        });
});
