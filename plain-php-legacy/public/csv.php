<?php
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/../inc/db.php';
require_login();

$res = mysqli_query($mysqli, "SELECT * FROM members WHERE status <> 2 ORDER BY id ASC");

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="members.csv"');

$out = "ID,氏名,カナ,メールアドレス,電話番号,都道府県,ランク,ステータス\n";
while ($m = mysqli_fetch_assoc($res)) {
    $out .= $m['id'] . ',';
    $out .= $m['name'] . ',';
    $out .= $m['name_kana'] . ',';
    $out .= $m['email'] . ',';
    $out .= $m['phone'] . ',';
    $out .= $m['prefecture'] . ',';
    $out .= rank_label($m['rank']) . ',';
    $out .= status_label($m['status']) . "\n";
}

echo $out;
