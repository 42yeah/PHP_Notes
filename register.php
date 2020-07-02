<?php 

require_once "common.php"; 

if (hasLoggedIn()) {
    alert("你已经登陆成功。");
    header("Location: /dashboard.php");
    return;
}

$valid = true;
if (($username = post("username")) && 
    ($password = post("password", null, "密码未设置。")) && 
    ($verification = post("verification", null, "验证密码未设置。")) &&
    ($question = post("safety-question", null, "安全问题未设置。")) && 
    ($answer = post("answer", null, "安全答案未设置。"))) {
    if ($password != $verification) {
        alert("两次密码必须一致。");
        $valid = false;
    }
    if ($question == "- 选择安全问题 -") {
        alert("选择有效的安全问题。");
        $valid = false;
    }
    if ($valid) {
        $conn = new mysqli("localhost", "root", "", "notes");
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if (count($stmt->get_result()->fetch_all()) <= 0) {
            $stmt = $conn->prepare("INSERT INTO users (username, password, question, answer) VALUES(?, ?, ?, ?);");
            $stmt->bind_param("ssss", $username, $password, $question, $answer);
            $stmt->execute();
            alert("注册成功。");
            header("Location: /login.php");
            return;
        } else {
            alert("账号 " . $username . " 已经存在。");
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注册 | Notes</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/common.js"></script>
    <script src="js/register.js"></script>
</head>
<body>
<div class="container">
    <div>
        <?php renderAlerts(); ?>
        <div class="header">
            注册 <strong>Notes</strong>
        </div>
        <form action="#" method="POST">
            <div>
                <?php post("username"); ?>
                <input class="cool-input" type="text" name="username" placeholder="账号..." value="<?php echo post("username", ""); ?>">
            </div>
            <div>
                <input class="cool-input" type="password" name="password" placeholder="密码..." value="<?php echo post("password", ""); ?>">
            </div>
            <div>
                <input class="cool-input" type="password" name="verification" placeholder="再次输入密码..." value="<?php echo post("verification", ""); ?>">
            </div>
            <div class="mt-1">
                <label for="safety-question">安全问题</label>
                <select id="safety-question" name="safety-question">
                    <option>- 选择安全问题 -</option>
                    <option>你最爱看的电影是什么？</option>
                    <option>你小时候上哪所小学？</option>
                    <option>你第二台手机的型号是什么？</option>
                    <option>你家邻居叫什么名字？</option>
                    <option>你最喜欢吃的菜的名字叫什么？</option>
                    <option>你最喜欢在什么地方旅游？</option>
                    <option>你喜欢什么豆腐脑，咸的还是甜的？</option>
                    <option>你小时候最爱欺负的人是谁？</option>
                    <option>你最爱的运动是什么？</option>
                    <option>你的存钱罐里有多少钱？</option>
                </select>
                <div id="answer" class="hidden">
                    <input name="answer" class="cool-input" placeholder="回答..." value="<?php echo post("answer", ""); ?>">
                </div>
            </div>
            <div>
                <button class="cool-button" type="submit">注册</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
