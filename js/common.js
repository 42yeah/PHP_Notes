(function() {
    function main() {
        const alerts = document.querySelector(".alerts");
        if (alerts) {
            alerts.addEventListener("click", () => {
                alerts.remove();
            });
        }
    }
    
    window.addEventListener("load", main);    
})();
