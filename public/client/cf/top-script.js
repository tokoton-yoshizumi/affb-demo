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

    const storedRef = localStorage.getItem("affiliate_ref");

    // `.contact-button` が複数ある場合にも対応
    document.querySelectorAll(".contact-button").forEach(function (button) {
        button.addEventListener("click", function (event) {
            if (!storedRef) return;

            event.preventDefault(); // デフォルトのリンク動作を防止

            let contactUrl = this.getAttribute("href");
            contactUrl +=
                (contactUrl.includes("?") ? "&" : "?") + "ref=" + storedRef;

            window.location.href = contactUrl;
        });
    });
});
