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
            // console.log(json);
            return;
        }
        // console.log(json);
        syncNoteList();
    });
}

function syncNoteList() {
    request(0, null).then(json => {
        if (json.state == "error") {
            // console.log(json);
            return;
        }
        // console.log(json);
        const list = json.data;
        renderList(list);
    });
}

function renderList(list) {
    const model = document.querySelector("#note-model");
    const listElem = document.querySelector(".note-list");
    const selected = document.querySelector(".selected");
    let selectedId = -1;
    if (selected) {
        selectedId = selected.getAttribute("note-id");
    }
    listElem.innerHTML = "";
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
}

function renderNote(id) {
    request(2, JSON.stringify({
        id: id
    })).then(json => {
        // console.log(json);
        const missing = document.querySelector(".missing");
        missing.classList.add("hidden");
        const main = document.querySelector(".main");
        main.classList.remove("hidden");
        const title = document.querySelector(".title");
        title.value = json.data[2];
        quill.setContents(json.data[7]);
        title.onchange = () => {
            const data = {
                id: id,
                title: title.value
            };
            request(3, JSON.stringify(data)).then(json => {
                if (json.state == "error") {
                    // console.log(json);
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
        // console.log(json);
        if (json.state != "success") {
            // console.log(json);
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
        // console.log(json);
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

let recording = false;
let gumStream = null;
let recordSession = null;
let millis = 0;

function startRecording() {
    const constraints = {
        audio: true,
        video: false
    } ;
    navigator.mediaDevices.getUserMedia(constraints).then(stream => {
        gumStream = stream;
        let ctx = new AudioContext();
        let input = ctx.createMediaStreamSource(stream);
        recordSession = new Recorder(input, {
            numChannels: 1
        });
        recordSession.record();
    });
}

function stopRecording(insertBlob) {
    recordSession.stop();
    gumStream.getAudioTracks()[0].stop();
    recordSession.exportWAV(insertBlob);
}

function updateRecordSeconds(elem) {
    if (recording) {
        millis += 1000;
        let lapsed = new Date(millis);
        let timeStr = lapsed.getMinutes() + ":" + lapsed.getSeconds();
        elem.innerHTML = timeStr;
        setTimeout(updateRecordSeconds, 1000, elem);
    }
}

function search() {
    const terms = document.querySelector(".search").value;
    if (terms == "") {
        syncNoteList();
        return;
    }
    request(6, JSON.stringify({
        terms
    })).then(json => {
        renderList(json.data);
    });
}

function main() {
    let BlockEmbed = Quill.import("blots/block/embed");

    class Source extends BlockEmbed {
        static create(value) {
            let node = super.create();
            node.src = value;
            node.innerText = "";
            window.v = value;
            return node;
        }
    }

    class Player extends BlockEmbed {
        static create(value) {
            let node = super.create();
            node.setAttribute("controls", "controls");
            node.setAttribute("autobuffer", "autobuffer");
            node.innerText = "";
            let source = Source.create(value);
            node.appendChild(source);
            return node;
        }

        static value(node) {
            return node.firstChild.getAttribute("src");
        }
    }

    Source.blotName = "source";
    Source.tagName = "source";
    Player.blotName = "player";
    Player.tagName = "audio";
    Player.allowedChildren = [ Source ];
    Source.allowedChildren = [ Player ];
    Quill.register(Source);
    Quill.register(Player);
    window.Source = Source;
    window.Player = Player;

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
    toolbar.addHandler("record", () => {
        // console.log("omega");
    });
    let recordButton = document.querySelector(".ql-record");
    let recordSvg = recordButton.querySelector("svg");
    let recordTime = recordButton.querySelector("div");
    recordButton.addEventListener("click", () => {
        let range = quill.getSelection();
        if (range) {
            // quill.insertText(range.index, "Ω");
            recording = !recording;
            if (recording) {
                recordSvg.classList.add("hidden");
                recordTime.classList.remove("hidden");
                startRecording();
                updateRecordSeconds(recordTime);
            } else {
                recordSvg.classList.remove("hidden");
                recordTime.classList.add("hidden");
                millis = 0;

                function insertBlob(blob) {
                    // let url = URL.createObjectURL(blob);
                    let reader = new FileReader();
                    reader.readAsDataURL(blob);
                    reader.onloadend = () => {
                        let data = reader.result;
                        quill.insertEmbed(range.index, "player", data);
                    };
                }

                stopRecording(insertBlob);
            }
        }
    });
    window.quill = quill;
    document.querySelector(".search").addEventListener("change", search);
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

