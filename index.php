<?php

require_once "common.php";

if (hasLoggedIn()) {
    header("Location: /dashboard.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>主页面 | Notes</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/common.js"></script>
    <script src="js/index.js"></script>
</head>
<body>
    <div class="blurry container">
    </div>
    <div class="container reverse-blurry">
        <div>
            <?php renderAlerts(); ?>
            <div class="header">
                欢迎使用 <strong>Notes</strong> 。
            </div>
            <div>
                <p>
                    <strong>Notes</strong> 是你日常做笔记的不二选择。<br />
                    <strong>Notes</strong> 支持：
                </p>
                <div class="carousel">
                    <div class="carousel-swooper">
                        <div class="carousel-item"><div><strong>语音</strong><br />笔记</div></div>
                        <div class="carousel-item"><div><strong>富</strong>文本</div></div>
                        <div class="carousel-item"><div>内嵌<strong><br />视频</strong></div></div>
                        <div class="carousel-item"><div><strong>快速</strong><br />搜索</div></div>
                        <div class="carousel-item"><div><strong>随时随地</strong><br />都可使用</div></div>
                    </div>
                </div>
                <a href="register.php" class="cool-button">前往注册</a><br />
                <a href="login.php" class="cool-button">登陆</a>
            </div>
        </div>
    </div>
</body>
</html>