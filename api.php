<?php

require_once "common.php";

function emitError($reason) {
    echo json_encode([ "state" => "error", "reason" => $reason ]);
    exit(0);
}

function success($ret) {
    echo json_encode([ "state" => "success", "data" => $ret ]);
    exit(0);
}

if (post("action") === null) {
    emitError("no action specified");
}
if (!is_numeric(post("action"))) {
    emitError("action must be a valid number");
}

function listNotes() {
    if (!hasLoggedIn()) { emitError("not logged in"); }
    $conn = new mysqli("127.0.0.1", "root", "", "notes");
    $stmt = $conn->prepare("SELECT * FROM notes WHERE owner=?");
    $stmt->bind_param("i", $_SESSION["id"]);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_all();
    success($res);
}

function appendNote() {
    if (!hasLoggedIn()) { emitError("not logged in"); }
    $conn = new mysqli("127.0.0.1", "root", "", "notes");
    $stmt = $conn->prepare("SELECT COUNT(*) FROM notes WHERE OWNER=?");
    $stmt->bind_param("i", $_SESSION["id"]);
    $stmt->execute();
    $newId = +$stmt->get_result()->fetch_all()[0][0] + 1;
    $stmt = $conn->prepare("INSERT INTO notes (owner, title, date) VALUES (?, ?, NOW())");
    $name = "未命名笔记 " . $newId;
    $stmt->bind_param("is", $_SESSION["id"], $name);
    $stmt->execute();
    $stmt = $conn->prepare("INSERT INTO note_content (note) VALUES (?)");
    $id = $conn->insert_id;
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $blobId = $conn->insert_id;
    $stmt = $conn->prepare("UPDATE notes SET blobId=? WHERE id=?");
    $stmt->bind_param("ii", $blobId, $id);
    $stmt->execute();
    success(null);
}

function getNoteContent() {
    if (!hasLoggedIn()) { emitError("not logged in"); }
    if (!post("data")) { emitError("no request data"); }
    $data = json_decode(post("data"), true);
    $conn = new mysqli("127.0.0.1", "root", "", "notes");
    $stmt = $conn->prepare("SELECT * FROM notes a INNER JOIN note_content b ON a.blobId=b.id WHERE a.id=? AND owner=?");
    $sessionId = $_SESSION["id"];
    $stmt->bind_param("ii", $data["id"], $sessionId);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all();
    if (count($rows) <= 0) {
        emitError("no data found");
    }
    $row = $rows[0];
    $row[7] = json_decode($row[7], true);
    success($row);
}

function changeTitle() {
    if (!hasLoggedIn()) { emitError("not logged in"); }
    if (!post("data")) { emitError("no request data"); }
    $data = json_decode(post("data"), true);
    $conn = new mysqli("127.0.0.1", "root", "", "notes");
    $stmt = $conn->prepare("UPDATE notes SET title=? WHERE id=? AND owner=?");
    $sessionId = $_SESSION["id"];
    $stmt->bind_param("sii", $data["title"], $data["id"], $sessionId);
    $stmt->execute();
    success($data);
}

function deleteNote() {
    if (!hasLoggedIn()) { emitError("not logged in"); }
    if (!post("data")) { emitError("no request data"); }
    $data = json_decode(post("data"), true);
    $conn = new mysqli("127.0.0.1", "root", "", "notes");
    $stmt = $conn->prepare("DELETE FROM notes WHERE id=? AND owner=?");
    $sessionId = $_SESSION["id"];
    $stmt->bind_param("ii", $data["id"], $sessionId);
    $stmt->execute();
    success($data);
}

function saveNote() {
    if (!hasLoggedIn()) { emitError("not logged in"); }
    if (!post("data")) { emitError("no request data"); }
    $data = json_decode(post("data"), true);
    $conn = new mysqli("127.0.0.1", "root", "", "notes");
    $stmt = $conn->prepare("SELECT blobId FROM notes WHERE id=? AND owner=?");
    $sessionId = $_SESSION["id"];
    $stmt->bind_param("ii", $data["id"], $sessionId);
    $stmt->execute();
    $blobId = $stmt->get_result()->fetch_all();
    if (count($blobId) <= 0) {
        emitError("could not find such a note");
    }
    $blobId = $blobId[0][0];
    $stmt = $conn->prepare("UPDATE note_content SET data=? WHERE id=?");
    $noteData = json_encode($data["note"]);
    $stmt->bind_param("si", $noteData, $blobId);
    $stmt->execute();
    success($blobId);
}

function searchNotes() {
    if (!hasLoggedIn()) { emitError("not logged in"); }
    if (!post("data")) { emitError("no request data"); }
    $data = json_decode(post("data"), true);
    $conn = new mysqli("127.0.0.1", "root", "", "notes");
    $terms = "%" . $data["terms"] . "%";
    // Search for titles first
    $sessionId = $_SESSION["id"];
    $stmt = $conn->prepare("SELECT * FROM notes WHERE owner=? AND title LIKE ?");
    $stmt->bind_param("is", $sessionId, $terms);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_all();

    // then date
    $stmt = $conn->prepare("SELECT * FROM notes WHERE owner=? AND DATE_FORMAT(date, '%Y-%m-%d') LIKE ? OR DATE_FORMAT(date, '%Y/%m/%d') LIKE ?");
    $stmt->bind_param("iss", $sessionId, $terms, $terms);
    $stmt->execute();
    $res = array_merge($res, $stmt->get_result()->fetch_all());

    // then content
    $stmt = $conn->prepare("SELECT * FROM notes a INNER JOIN note_content b ON a.blobId=b.id WHERE owner=? AND data LIKE ?");
    $stmt->bind_param("is", $sessionId, $terms);
    $stmt->execute();
    $res = array_merge($res, $stmt->get_result()->fetch_all());

    for ($i = 0; $i < count($res); $i++) {
        for ($j = $i + 1; $j < count($res); $j++) {
            if ($res[$i][0] == $res[$j][0]) {
                $res = array_splice($res, $j, 1);
                $j--;
                continue;
            }
        }
    }
    success($res);
}

// Available actions:
// 0: List notes of a user
// 1: Append a new note to the note list
// 2: Get the content of a particular note
// 3: Change the title of a given note
// 4: Delete given note
// 5: Save the blob of given note
// 6: Search for notes

switch (post("action")) {
case 0:
    listNotes();
    break;

case 1:
    appendNote();
    break;

case 2:
    getNoteContent();
    break;

case 3:
    changeTitle();
    break;

case 4:
    deleteNote();
    break;

case 5:
    saveNote();
    break;

case 6:
    searchNotes();
    break;

default:
    emitError("unknown action");
    break;
}
