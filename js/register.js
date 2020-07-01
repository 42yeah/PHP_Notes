(function() {
    function main() {
        const question = document.querySelector("#safety-question");
        const answer = document.querySelector("#answer");
        question.addEventListener("change", () => {
            if (question.value != "- 选择安全问题 -") {
                answer.classList.remove("hidden");
            } else {
                answer.classList.add("hidden");
            }
        });
    }

    window.addEventListener("load", main);
})();
