(function() {
    function getCookie(name) {
        let cookie = document.cookie.match(new RegExp("(?:^|; )" + name + "=([^;]*)"));
        return cookie ? decodeURIComponent(cookie[1]) : null;
    }

    function main() {
        let remember = document.querySelector("#remember");
        let fields = document.querySelectorAll("input:not([name='captcha'])");
        for (let i = 0; i < fields.length; i++) {
            const field = fields[i];
            let cookie = getCookie(field.name);
            field.value = cookie;
            if (field.type == "checkbox") {
                field.checked = cookie == "on";
            }
            field.addEventListener("change", () => {
                if (remember.checked) {
                    document.cookie = field.name + "=" + field.value;
                }
            });
        }
        remember.addEventListener("change", () => {
            if (!remember.checked) {
                for (let i = 0; i < fields.length; i++) {
                    document.cookie = fields[i].name + "=";
                }
            } else {
                for (let i = 0; i < fields.length; i++) {
                    let value = fields[i].value;
                    if (fields[i].type == "checkbox") {
                        value = fields[i].checked ? "on" : "";
                    }
                    document.cookie = fields[i].name + "=" + value;
                }
            }
        });
    }

    window.addEventListener("load", main);
})();
