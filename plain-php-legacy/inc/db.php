<?php
require_once __DIR__ . '/config.php';

// DB接続（mysqli 手続き型）
$mysqli = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$mysqli) {
    die('DB接続に失敗しました: ' . mysqli_connect_error());
}
mysqli_set_charset($mysqli, 'utf8mb4');
