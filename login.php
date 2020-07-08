<?php 

require_once "common.php"; 

if (hasLoggedIn()) {
    alert("你已经登陆成功。");
    header("Location: /dashboard.php");
    return;
}

if (($username = post("username")) &&
    ($password = post("password", null, "未输入密码。"))) {
    $conn = new mysqli("127.0.0.1", "root", "", "notes");
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = $res->fetch_all();
    if (count($rows) <= 0) {
        alert("账户名或密码错误。");
    } else {
        $_SESSION["username"] = $username;
        $_SESSION["id"] = $rows[0][0];
        header("Location: /dashboard.php");
        return;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登陆 | Notes</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/common.js"></script>
    <script src="js/login.js"></script>
</head>
<body>
    <div class="container">
        <div>
            <?php renderAlerts(); ?>
            <div class="header">
                登陆至 <strong>Notes</strong>
            </div>
            <form action="#" method="POST">
                <div>
                    <input class="cool-input" type="text" name="username" placeholder="账号...">
                </div>
                <div>
                    <input class="cool-input" type="password" name="password" placeholder="密码...">
                </div>
                <div class="mt-1">
                    <input id="remember" name="remember" type="checkbox">
                    <label for="remember">记住我</label>
                </div>
                <div>
                    <button class="cool-button" type="submit">登陆</button>
                </div>
                <div class="mt-1">
                    没有账号？<a class="cool-link" href="register.php">注册</a><br />
                    忘记密码？<a class="cool-link" href="recover.php">找回</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>