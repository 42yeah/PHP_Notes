function request(action, data) {
    const formData = new FormData();
    formData.append("action", action);
    formData.append("data", data);
    let response = null;
    return fetch("/api.php", {
        method: "POST",
        mode: "cors",
        body: formData
    }).then(res => {
        response = res;
        return res.json();
    }).catch(e => {
        if (!response) {
            return {
                state: "error",
                reason: "no response from server"
            }
        }
        return {
            state: "error",
            reason: "unknown"
        };
    });
}

function addNote() {
    request(1, null).then(json => {
        if (json.state == "error") {
            console.log(json);
            return;
        }
        console.log(json);
        syncNoteList();
    });
}

function syncNoteList() {
    request(0, null).then(json => {
        if (json.state == "error") {
            console.log(json);
            return;
        }
        console.log(json);
        const model = document.querySelector("#note-model");
        const listElem = document.querySelector(".note-list");
        const selected = document.querySelector(".selected");
        let selectedId = -1;
        if (selected) {
            selectedId = selected.getAttribute("note-id");
        }
        listElem.innerHTML = "";
        const list = json.data;
        for (let i = 0; i < list.length; i++) {
            const elem = model.cloneNode();
            elem.classList.remove("hidden");
            elem.classList.remove("model");
            elem.setAttribute("note-id", list[i][0]);
            elem.innerText = list[i][2];
            elem.addEventListener("click", selectNode);
            if (list[i][0] == selectedId) {
                elem.classList.add("selected");
            }
            listElem.appendChild(elem);
        }
    });
}

function renderNote(id) {
    request(2, JSON.stringify({
        id: id
    })).then(json => {
        console.log(json);
    });
}

function selectNode(e) {
    const target = e.target;
    const listElem = document.querySelector(".note-list");
    for (let i = 0; i < listElem.children.length; i++) {
        listElem.children[i].classList.remove("selected");
    }
    target.classList.add("selected");
    renderNote(+target.getAttribute("note-id"));
}

function main() {
    syncNoteList();
}

window.addEventListener("load", main);
