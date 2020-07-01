<?php require_once "common.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>仪表板 | Notes</title>
    <link rel="stylesheet" href="css/styles.css">
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
                <div class="legend">新增笔记<a href="javascript:addNote()" class="float-right round-button"></a></div>
                <div id="note-model" class="note-title hidden model">model</div>
                <div class="note-list">
                </div>
            </div>
            <div class="note-content">
                
            </div>
        </div>
    </div>
    
</body>
</html>
