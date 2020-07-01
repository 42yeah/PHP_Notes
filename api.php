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
    success(json_encode($res));
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
    success(null);
}

// Available actions:
// 0: List notes of a particular user
// 1: Append a new note to the note list

switch (post("action")) {
case 0:
    listNotes();
    break;

case 1:
    appendNote();
    break;
}
