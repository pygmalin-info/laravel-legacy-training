<?php
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/../inc/db.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $kana = $_POST['name_kana'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $zip = $_POST['postal_code'];
    $pref = $_POST['prefecture'];
    $address = $_POST['address'];
    $memo = $_POST['memo'];

    // ランク判定
    $flg = $_POST['rank'];
    if ($flg == '') {
        $rank = 1;
    } else {
        if ($flg == 'gold') {
            $rank = 2;
        } else {
            if ($flg == 'platinum') {
                $rank = 3;
            } else {
                $rank = 1;
            }
        }
    }

    // 登録直後は仮登録
    $status = 0;

    // アバター画像
    $avatar = '';
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        $fname = time() . '_' . $_FILES['avatar']['name'];
        move_uploaded_file($_FILES['avatar']['tmp_name'], __DIR__ . '/uploads/' . $fname);
        $avatar = 'uploads/' . $fname;
    }

    $sql = "INSERT INTO members (name, name_kana, email, phone, gender, birthday, postal_code, prefecture, address, `rank`, status, memo, avatar)
            VALUES ('$name', '$kana', '$email', '$phone', '$gender', '$birthday', '$zip', '$pref', '$address', '$rank', '$status', '$memo', '$avatar')";
    mysqli_query($mysqli, $sql);

    // 登録完了メール
    if ($status == 1) {
        $body = $name . " 様\n\nご登録ありがとうございます。";
        $headers = "From: " . FROM_MAIL . "\r\n";
        $headers .= "Bcc: " . ADMIN_MAIL . "\r\n";
        mail($email, '会員登録が完了しました', $body, $headers);
    }

    header('Location: index.php');
    exit;
}

render_header('会員登録');
?>
<h1>会員登録</h1>
<form method="post" action="member_add.php">
    <div class="form-group"><label>氏名 <span class="req">必須</span></label><input type="text" name="name"></div>
    <div class="form-group"><label>カナ</label><input type="text" name="name_kana"></div>
    <div class="form-group"><label>メールアドレス <span class="req">必須</span></label><input type="text" name="email"></div>
    <div class="form-group"><label>電話番号</label><input type="text" name="phone"></div>
    <div class="form-group"><label>性別</label>
        <select name="gender"><option value="1">男性</option><option value="2">女性</option><option value="3">その他</option></select>
    </div>
    <div class="form-group"><label>生年月日</label><input type="date" name="birthday"></div>
    <div class="form-group"><label>郵便番号</label><input type="text" name="postal_code"></div>
    <div class="form-group"><label>都道府県</label><input type="text" name="prefecture"></div>
    <div class="form-group"><label>住所</label><input type="text" name="address"></div>
    <div class="form-group"><label>ランク</label>
        <select name="rank"><option value="">通常</option><option value="gold">ゴールド</option><option value="platinum">プラチナ</option></select>
    </div>
    <div class="form-group"><label>アバター画像</label><input type="file" name="avatar"></div>
    <div class="form-group"><label>メモ</label><textarea name="memo"></textarea></div>
    <button type="submit" class="btn btn-primary">登録する</button>
    <a href="index.php" class="btn">戻る</a>
</form>
<?php
render_footer();
