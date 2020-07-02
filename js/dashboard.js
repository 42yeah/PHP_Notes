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
        const missing = document.querySelector(".missing");
        missing.classList.add("hidden");
        const main = document.querySelector(".main");
        main.classList.remove("hidden");
        const title = document.querySelector(".title");
        title.value = json.data[2];
        quill.setContents(json.data[6]);
        title.onchange = () => {
            const data = {
                id: id,
                title: title.value
            };
            request(3, JSON.stringify(data)).then(json => {
                if (json.state == "error") {
                    console.log(json);
                    return;
                }
                syncNoteList();
            });
        };
        document.querySelector("#delete").onclick = () => {
            deleteNote(id);
            missing.classList.remove("hidden");
            main.classList.add("hidden");
        };
        document.querySelector("#save").onclick = () => {
            save(id);
        };
    });
}

function deleteNote(id) {
    request(4, JSON.stringify({ id })).then(json => {
        console.log(json);
        if (json.state != "success") {
            console.log(json);
            return;
        }
        syncNoteList();
    })
}

function save(id) {
    request(5, JSON.stringify({
        id: id,
        note: quill.getContents()
    })).then(json => {
        console.log(json);
        aussuringGreenTitle();
    })
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

    const quill = new Quill('#editor-container', {
        modules: {
            formula: true,
            syntax: true,
            toolbar: '#toolbar-container'
        },
        placeholder: '写点儿啥...',
        theme: 'snow'
    });
    const toolbar = quill.getModule("toolbar");
    toolbar.addHandler("omega", () => {
        console.log("omega");
    });
    document.querySelector(".ql-record").addEventListener("click", () => {
        let range = quill.getSelection();
        if (range) {
            quill.insertText(range.index, "Ω");
        }
    });
    window.quill = quill;
}

function aussuringGreenTitle() {
    const title = document.querySelector(".title");
    title.style.color = "#329255";
    setTimeout(() => {
        title.style.color = "black";
    }, 150);
}

function logOut() {
    document.cookie = "PHPSESSID=";
    window.location.href = "/login.php";
}

window.onkeydown = (e) => {
    if ((e.key == "s" && e.ctrlKey) ||
        (e.key == "s" && e.metaKey)) {
        e.preventDefault();

        const selected = document.querySelector(".selected");
        if (selected) {
            save(+selected.getAttribute("note-id"));
        }
    }
};

window.addEventListener("load", main);

