<?php

require_once "common.php";

if (!hasLoggedIn()) {
    header("Location: /login.php");
    alert("你需要先登录。");
    exit(0);
}

