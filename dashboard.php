<?php 

require_once "common.php"; 

if (!hasLoggedIn()) {
    alert("你需要先登录。");
    header("Location: /login.php");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>仪表板 | Notes</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.7.1/katex.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/monokai-sublime.min.css">
    <link rel="stylesheet" href="css/quill.snow.css">
    <script src="js/common.js"></script>
    <script src="js/dashboard.js"></script>
</head>
<body>
    <div class="d-flex">
        <div class="fill">
            <div class="sidebar">
                <div class="banner">
                    <a class="banner-link" href="href="/dashboard.php">Notes | <strong>笔记管理</strong></a>
                </div>
                <div class="legend">
                    <?php renderAlerts(); ?>
                    你好，<?php echo $_SESSION["username"]; ?>！<a href="javascript:logOut();" class="cool-link">登出</a>
                </div>
                <div class="legend">新增笔记<a href="javascript:addNote()" class="float-right round-button"></a></div>
                <div id="note-model" class="note-elem hidden model">model</div>
                <div class="note-list">
                </div>
            </div>
            <div class="note-content-wrapper">
                <div class="note-content">
                    <div class="missing">
                        <div class="missing-content">
                            选中笔记以开始编辑
                        </div>
                    </div>
                    <div class="hidden padding main">
                        <div>
                            <input class="title">
                        </div>
                        <div class="sticky">
                            <a href="javascript:void(0);" class="cool-button" id="save">保存</a>
                            <a href="javascript:void(0);" class="cool-button" id="delete">删除</a>
                        </div>
                        <div>
                            <div id="standalone-container">
                                <div id="toolbar-container">
                                    <span class="ql-formats">
                                        <select class="ql-font"></select>
                                        <select class="ql-size"></select>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-bold"></button>
                                        <button class="ql-italic"></button>
                                        <button class="ql-underline"></button>
                                        <button class="ql-strike"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <select class="ql-color"></select>
                                        <select class="ql-background"></select>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-script" value="sub"></button>
                                        <button class="ql-script" value="super"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-header" value="1"></button>
                                        <button class="ql-header" value="2"></button>
                                        <button class="ql-blockquote"></button>
                                        <button class="ql-code-block"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-list" value="ordered"></button>
                                        <button class="ql-list" value="bullet"></button>
                                        <button class="ql-indent" value="-1"></button>
                                        <button class="ql-indent" value="+1"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-direction" value="rtl"></button>
                                        <select class="ql-align"></select>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-link"></button>
                                        <button class="ql-image"></button>
                                        <button class="ql-video"></button>
                                        <button class="ql-formula"></button>
                                        <button class="ql-record">
                                            <svg t="1593679892848" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="1128" width="18" height="18"><path class="ql-even ql-stroke ql-fill" d="M512 938.666667C276.352 938.666667 85.333333 747.648 85.333333 512S276.352 85.333333 512 85.333333s426.666667 191.018667 426.666667 426.666667-191.018667 426.666667-426.666667 426.666667z m0-85.333334a341.333333 341.333333 0 1 0 0-682.666666 341.333333 341.333333 0 0 0 0 682.666666z m0-213.333333a128 128 0 1 1 0-256 128 128 0 0 1 0 256z" p-id="1129"></path></svg>
                                            <div class="hidden">0:0</div>
                                        </button>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-clean"></button>
                                    </span>
                                </div>
                                <div id="editor-container"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.7.1/katex.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script>
    <script src="js/quill.min.js"></script>
    <script src="js/recorder.js"></script>
</body>
</html>
