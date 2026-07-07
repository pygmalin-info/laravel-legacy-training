<?php
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/../inc/db.php';
require_login();

$id = $_GET['id'];
$res = mysqli_query($mysqli, "SELECT * FROM members WHERE id = " . $id);
$m = mysqli_fetch_assoc($res);
if (!$m) {
    echo '会員が見つかりません';
    exit;
}

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

render_header('会員詳細');
?>
<div class="page-head">
    <h1><?php echo h($m['name']); ?> さんの情報</h1>
    <div>
        <a href="member_edit.php?id=<?php echo $m['id']; ?>" class="btn">編集</a>
    </div>
</div>

<?php if ($msg !== '') { echo '<p class="success">' . $msg . '</p>'; } ?>

<table class="detail">
    <tr><th>ID</th><td><?php echo $m['id']; ?></td></tr>
    <tr><th>氏名</th><td><?php echo h($m['name']); ?></td></tr>
    <tr><th>カナ</th><td><?php echo h($m['name_kana']); ?></td></tr>
    <tr><th>メールアドレス</th><td><?php echo h($m['email']); ?></td></tr>
    <tr><th>電話番号</th><td><?php echo h($m['phone']); ?></td></tr>
    <tr><th>性別</th><td><?php echo $m['gender'] == 1 ? '男性' : ($m['gender'] == 2 ? '女性' : 'その他'); ?></td></tr>
    <tr><th>生年月日</th><td><?php echo date('Y年n月j日', (int)$m['birthday']); ?></td></tr>
    <tr><th>住所</th><td><?php echo h($m['prefecture'] . $m['address']); ?></td></tr>
    <tr><th>ランク</th><td><?php echo rank_label($m['rank']); ?></td></tr>
    <tr><th>ステータス</th><td><?php echo status_label($m['status']); ?></td></tr>
    <tr><th>メモ</th><td><?php echo $m['memo']; ?></td></tr>
    <?php if ($m['avatar']) { ?>
    <tr><th>アバター</th><td><img src="<?php echo $m['avatar']; ?>" class="avatar"></td></tr>
    <?php } ?>
</table>

<div class="mail-box">
    <h2>メール送信</h2>
    <form method="post" action="mail.php">
        <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
        <textarea name="body" placeholder="本文"></textarea>
        <button type="submit" class="btn btn-primary">このメンバーに送信</button>
    </form>
</div>

<p><a href="index.php">← 一覧へ戻る</a></p>
<?php
render_footer();
