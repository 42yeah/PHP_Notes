function main() {
    let swooper = document.querySelector(".carousel-swooper");
    let numItems = swooper.childElementCount;
    let current = 0;
    
    function swoop() {
        current++;
        if (current >= numItems) { current = 0; }
        swooper.style.transform = "translateY(-" + current + "00%)";
        setTimeout(swoop, 5000);
    }
    setTimeout(swoop, 5000);
}

window.addEventListener("load", main);
