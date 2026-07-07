<?php
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/../inc/db.php';
require_login();

$id = $_POST['id'];
$body = $_POST['body'];

$res = mysqli_query($mysqli, "SELECT * FROM members WHERE id = " . $id);
$m = mysqli_fetch_assoc($res);

$text = $m['name'] . " 様\n\n" . $body;
$headers = "From: " . FROM_MAIL . "\r\n";
$headers .= "Bcc: " . ADMIN_MAIL . "\r\n";
mail($m['email'], '会員管理システムからのお知らせ', $text, $headers);

header('Location: member_view.php?id=' . $id . '&msg=' . rawurlencode('メールを送信しました'));
exit;
