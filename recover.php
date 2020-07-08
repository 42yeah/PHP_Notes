<?php 

require_once "common.php"; 

if (hasLoggedIn()) {
    alert("你已经登陆成功，为什么要找回密码？");
    header("Location: /dashboard.php");
    return;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>找回密码 | Notes</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/common.js"></script>
    <script src="js/recover.js"></script>
</head>
<body>
    <div class="container">
        <div>

<?php

if (!isset($_SESSION["recoverSteps"]) || isset($_GET["restart"])) {
    $_SESSION["recoverSteps"] = 0;
}
if (isset($_GET["restart"])) {
    echo "<script>redirect(\"/recover.php\");</script>";
}

$hasUser = post("username", false);
$conn = new mysqli("127.0.0.1", "root", "", "notes");
$id = -1;
if ($hasUser) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
    $stmt->bind_param("s", $hasUser);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all();
    $hasUser = count($rows) > 0;
    if (!$hasUser) { alert("不存在此用户。"); }
    else {
        $id = $rows[0][0]; 
        $_SESSION["recoverId"] = $id;
        $_SESSION["recoverSteps"] = 1;
    }
}
if ($_SESSION["recoverSteps"] == 0) {
?>

            <?php renderAlerts(); ?>
            <div class="header">
                从 <strong>Notes</strong> 找回密码
            </div>
            <form action="#" method="POST">
                <div>
                    <input class="cool-input" type="text" name="username" placeholder="账号...">
                </div>
                <div>
                    <button class="cool-button" type="submit">开始找回</button>
                </div>
                <div class="mt-1">
                    想起来了？<a class="cool-link" href="login.php">登陆</a><br />
                </div>
            </form>

<?php
    return;
}

$id = $_SESSION["recoverId"];
$stmt = $conn->prepare("SELECT question, answer FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$question = $stmt->get_result()->fetch_all()[0];
$answer = $question[1];
$question = $question[0];

if (post("answer")) {
    if (post("answer") == $answer) {
        $_SESSION["recoverSteps"] = 3;
    } else {
        alert("安全问题回答错误。");
    }
}

if ($_SESSION["recoverSteps"] == 1) {
?>

            <?php renderAlerts(); ?>
            <div class="header">
                回答以下安全问题：<br /><strong><?php echo $question; ?></strong>
            </div>
            <form action="#" method="POST">
                <div>
                    <input class="cool-input" type="text" name="answer" placeholder="回答...">
                </div>
                <div>
                    <button class="cool-button" type="submit">下一步</button>
                </div>
                <div class="mt-1">
                    想起来了？<a class="cool-link" href="login.php">登陆</a><br />
                    要找的不是这个账号？<a class="cool-link" href="recover.php?restart=1">从头来过</a>
                </div>
            </form>

<?php
    return;
}

if (post("password")) {
    if (post("password") == post("verification")) {
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $id = $_SESSION["recoverId"];
        $password = post("password");
        $stmt->bind_param("si", $password, $id);
        $stmt->execute();
        alert("密码修改完成。");
        unset($_SESSION["recoverId"]);
        unset($_SESSION["recoverSteps"]);
        echo "<script>redirect(\"/login.php\");</script>";
        return;
    }
}

if ($_SESSION["recoverSteps"] == 3) {
?>

            <?php renderAlerts(); ?>
            <div class="header">
                修改新的 <strong>密码</strong>
            </div>
            <form action="#" method="POST">
                <div>
                    <input class="cool-input" type="password" name="password" placeholder="密码..." value="">
                </div>
                <div>
                    <input class="cool-input" type="password" name="verification" placeholder="再次输入密码..." value="">
                </div>
                <div>
                    <button class="cool-button" type="submit">下一步</button>
                </div>
                <div class="mt-1">
                    想起来了？<a class="cool-link" href="login.php">登陆</a><br />
                    要找的不是这个账号？<a class="cool-link" href="recover.php?restart=1">从头来过</a>
                </div>
            </form>

<?php
}
?>

        </div>
    </div>
</body>
</html>
