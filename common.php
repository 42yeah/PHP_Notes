<?php

session_start();

function hasLoggedIn() {
    return isset($_SESSION["username"]) && $_SESSION["username"] != null;
}

function alert($msg) {
    if (!isset($_SESSION["alerts"])) {
        $_SESSION["alerts"] = [];
    }
    array_push($_SESSION["alerts"], $msg);
}

function getAlerts() {
    if (!isset($_SESSION["alerts"])) {
        $_SESSION["alerts"] = [];
    }
    return $_SESSION["alerts"];
}

function renderAlerts() {
    if (count(getAlerts()) <= 0) { return; }
    echo "<div class=\"alerts\">";
    for ($i = 0; $i < count(getAlerts()); $i++) {
        echo getAlerts()[$i] . "<br />";
    }
    echo "</div>";
    $_SESSION["alerts"] = [];
    return;
}

function post($key, $otherwise = null, $warn = null) {
    if (isset($_POST[$key]) && $_POST[$key] !== null && $_POST[$key] !== "") {
        return $_POST[$key];
    }
    if ($warn) {
        alert($warn);
    }
    return $otherwise;
}
