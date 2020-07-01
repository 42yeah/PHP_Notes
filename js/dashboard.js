function request(action, data) {
    const formData = new FormData();
    formData.append("action", action);
    formData.append("data", data);
    return fetch("/api.php", {
        method: "POST",
        mode: "cors",
        body: formData
    }).then(res => {
        return res.json();
    });
}

function addNote() {
    request(1, null).then(json => {
        if (json.state == "error") {
            console.log(json);
            return;
        }
        syncNoteList();
    });
}

function syncNoteList() {
    request(0, null).then(json => {
        if (json.state == "error") {
            console.log(json);
            return;
        }
        const model = document.querySelector("#note-model");
        const listElem = document.querySelector(".note-list");
        listElem.innerHTML = "";
        const list = JSON.parse(json.data);
        for (let i = 0; i < list.length; i++) {
            const elem = model.cloneNode();
            elem.classList.remove("hidden");
            elem.classList.remove("model");
            elem.innerText = list[i][2];
            listElem.appendChild(elem);
        }
    });
}

function main() {
    syncNoteList();
}

window.addEventListener("load", main);
