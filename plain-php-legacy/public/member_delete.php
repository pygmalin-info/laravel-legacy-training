<?php
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/../inc/db.php';
require_login();

// 削除対象のID
$id = isset($_POST['id']) ? $_POST['id'] : '';

if ($id != '') {
    mysqli_query($mysqli, "DELETE FROM members WHERE id = " . $id);
}

header('Location: index.php');
exit;
