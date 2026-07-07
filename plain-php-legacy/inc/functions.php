<?php
// 共通関数いろいろ

session_start();

// ログインしていなければログイン画面へ
function require_login()
{
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

// HTMLエスケープ（使っている箇所と使っていない箇所がある…）
function h($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// ランク表示
function rank_label($rank)
{
    if ($rank == 1) return '通常';
    if ($rank == 2) return 'ゴールド';
    if ($rank == 3) return 'プラチナ';
    return '';
}

// ステータス表示
function status_label($status)
{
    if ($status == 0) return '仮登録';
    if ($status == 1) return '有効';
    if ($status == 2) return '退会';
    return '';
}

// ヘッダ出力
function render_header($title)
{
    $name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
    echo '<!DOCTYPE html><html lang="ja"><head><meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . $title . '</title><link rel="stylesheet" href="css/style.css"></head><body>';
    echo '<header class="header"><a href="index.php" class="logo">会員管理システム</a>';
    if ($name !== '') {
        echo '<span>' . h($name) . ' さん <a href="logout.php">ログアウト</a></span>';
    }
    echo '</header><main class="container">';
}

function render_footer()
{
    echo '</main></body></html>';
}
