<?php
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/../inc/db.php';
require_login();

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $kana = $_POST['name_kana'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $zip = $_POST['postal_code'];
    $pref = $_POST['prefecture'];
    $address = $_POST['address'];
    $status = $_POST['status'];
    $memo = $_POST['memo'];

    $sql = "UPDATE members SET
                name = '$name',
                email = '$email',
                gender = '$gender',
                birthday = '$birthday',
                postal_code = '$zip',
                prefecture = '$pref',
                address = '$address',
                status = '$status',
                memo = '$memo',
                updated_at = NOW()
            WHERE id = " . $id;
    mysqli_query($mysqli, $sql);

    header('Location: member_view.php?id=' . $id . '&msg=' . rawurlencode('更新しました'));
    exit;
}

$res = mysqli_query($mysqli, "SELECT * FROM members WHERE id = " . $id);
$m = mysqli_fetch_assoc($res);
if (!$m) {
    echo '会員が見つかりません';
    exit;
}

render_header('会員編集');
?>
<h1>会員編集</h1>
<form method="post" action="member_edit.php">
    <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
    <div class="form-group"><label>氏名</label><input type="text" name="name" value="<?php echo h($m['name']); ?>"></div>
    <div class="form-group"><label>カナ</label><input type="text" name="name_kana" value="<?php echo h($m['name_kana']); ?>"></div>
    <div class="form-group"><label>メールアドレス</label><input type="text" name="email" value="<?php echo h($m['email']); ?>"></div>
    <div class="form-group"><label>電話番号</label><input type="text" name="phone" value="<?php echo h($m['phone']); ?>"></div>
    <div class="form-group"><label>性別</label>
        <select name="gender">
            <option value="1" <?php if ($m['gender'] == 1) echo 'selected'; ?>>男性</option>
            <option value="2" <?php if ($m['gender'] == 2) echo 'selected'; ?>>女性</option>
            <option value="3" <?php if ($m['gender'] == 3) echo 'selected'; ?>>その他</option>
        </select>
    </div>
    <div class="form-group"><label>生年月日</label><input type="date" name="birthday" value="<?php echo h($m['birthday']); ?>"></div>
    <div class="form-group"><label>郵便番号</label><input type="text" name="postal_code" value="<?php echo h($m['postal_code']); ?>"></div>
    <div class="form-group"><label>都道府県</label><input type="text" name="prefecture" value="<?php echo h($m['prefecture']); ?>"></div>
    <div class="form-group"><label>住所</label><input type="text" name="address" value="<?php echo h($m['address']); ?>"></div>
    <div class="form-group"><label>ステータス</label>
        <select name="status">
            <option value="0" <?php if ($m['status'] == 0) echo 'selected'; ?>>仮登録</option>
            <option value="1" <?php if ($m['status'] == 1) echo 'selected'; ?>>有効</option>
            <option value="2" <?php if ($m['status'] == 2) echo 'selected'; ?>>退会</option>
        </select>
    </div>
    <div class="form-group"><label>メモ</label><textarea name="memo"><?php echo h($m['memo']); ?></textarea></div>
    <button type="submit" class="btn btn-primary">更新する</button>
    <a href="member_view.php?id=<?php echo $m['id']; ?>" class="btn">戻る</a>
</form>
<?php
render_footer();
