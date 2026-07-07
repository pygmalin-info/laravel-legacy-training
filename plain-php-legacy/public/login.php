<?php
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/../inc/db.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    // メールとパスワードで認証
    $sql = "SELECT * FROM users WHERE email = '" . $email . "' AND password = '" . md5($pass) . "' LIMIT 1";
    $res = mysqli_query($mysqli, $sql);
    $row = mysqli_fetch_assoc($res);

    if ($row) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['name'];
        header('Location: index.php');
        exit;
    } else {
        $err = 'メールアドレスまたはパスワードが違います。';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ログイン</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-box">
    <h1>ログイン</h1>
    <?php if ($err !== '') { echo '<p class="error">' . $err . '</p>'; } ?>
    <form method="post" action="login.php">
        <div class="form-group">
            <label>メールアドレス</label>
            <input type="text" name="email">
        </div>
        <div class="form-group">
            <label>パスワード</label>
            <input type="password" name="password">
        </div>
        <button type="submit" class="btn btn-primary">ログイン</button>
    </form>
    <p class="hint">初期アカウント: admin@example.com / password</p>
</div>
</body>
</html>
