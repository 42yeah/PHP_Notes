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
    $conn = new mysqli("localhost", "root", "", "notes");
    $stmt = $conn->prepare("SELECT * FROM notes WHERE owner=?");
    $stmt->bind_param("i", $_SESSION["id"]);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_all();
    success($res);
}

function appendNote() {
    if (!hasLoggedIn()) { emitError("not logged in"); }
    $conn = new mysqli("localhost", "root", "", "notes");
    $stmt = $conn->prepare("SELECT COUNT(*) FROM notes WHERE OWNER=?");
    $stmt->bind_param("i", $_SESSION["id"]);
    $stmt->execute();
    $newId = +$stmt->get_result()->fetch_all()[0][0] + 1;
    $stmt = $conn->prepare("INSERT INTO notes (owner, title) VALUES (?, ?)");
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
    if (!post("data")) { emitError("no request"); }
    $data = json_decode(post("data"), true);
    $conn = new mysqli("localhost", "root", "", "notes");
    $stmt = $conn->prepare("SELECT * FROM notes a INNER JOIN note_content b ON a.blobId=b.id WHERE a.id=? AND owner=?");
    $sessionId = $_SESSION["id"];
    $stmt->bind_param("ii", $data["id"], $sessionId);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all();
    if (count($rows) <= 0) {
        emitError("no data found");
    }
    $row = $rows[0];
    success($row);
}

// Available actions:
// 0: List notes of a user
// 1: Append a new note to the note list
// 2: Get the content of a particular note

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
}
