<?php
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/../inc/db.php';
require_login();

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$status  = isset($_GET['status'])  ? $_GET['status']  : '';
$pref    = isset($_GET['pref'])    ? $_GET['pref']    : '';
$phone   = isset($_GET['phone'])   ? $_GET['phone']   : '';

// 検索条件を組み立てる
$where = " WHERE 1 ";
if ($keyword != '') {
    $where .= " AND name LIKE '%" . $keyword . "%' OR name_kana LIKE '%" . $keyword . "%' OR email LIKE '%" . $keyword . "%' ";
}
if ($status != '') {
    $where .= " AND status = " . $status . " ";
} else {
    $where .= " AND status <> 2 ";
}
if ($pref != '') {
    $where .= " AND prefecture = '" . $pref . "' ";
}
if ($phone != '') {
    $where .= " AND phone LIKE '%" . $phone . "%' ";
}

// 並び替え
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) ? $_GET['order'] : 'desc';

// ページング
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * PER_PAGE;

$sql = "SELECT * FROM members" . $where . " ORDER BY " . $sort . " " . $order . " LIMIT " . PER_PAGE . " OFFSET " . $offset;
$res = mysqli_query($mysqli, $sql);

// 総件数
$cntRes = mysqli_query($mysqli, "SELECT COUNT(*) AS c FROM members" . $where);
$total = mysqli_fetch_assoc($cntRes)['c'];
$maxPage = ceil($total / PER_PAGE);

render_header('会員一覧');
?>
<div class="page-head">
    <h1>会員一覧</h1>
    <div>
        <a href="member_add.php" class="btn btn-primary">新規登録</a>
        <a href="csv.php" class="btn">CSV出力</a>
    </div>
</div>

<?php if ($keyword != '') { echo '<p class="searchword">「' . $keyword . '」の検索結果</p>'; } ?>

<form method="get" action="index.php" class="search-form">
    <input type="text" name="keyword" value="<?php echo $keyword; ?>" placeholder="氏名・カナ・メール">
    <input type="text" name="phone" value="<?php echo $phone; ?>" placeholder="電話番号">
    <select name="status">
        <option value="">ステータス</option>
        <option value="0" <?php if ($status === '0') echo 'selected'; ?>>仮登録</option>
        <option value="1" <?php if ($status === '1') echo 'selected'; ?>>有効</option>
        <option value="2" <?php if ($status === '2') echo 'selected'; ?>>退会</option>
    </select>
    <input type="text" name="pref" value="<?php echo $pref; ?>" placeholder="都道府県">
    <button type="submit" class="btn">検索</button>
</form>

<table class="list">
    <thead>
        <tr>
            <th><a href="index.php?sort=id&order=asc">ID</a></th>
            <th><a href="index.php?sort=name&order=asc">氏名</a></th>
            <th>メールアドレス</th>
            <th>都道府県</th>
            <th>同県会員</th>
            <th><a href="index.php?sort=rank&order=asc">ランク</a></th>
            <th>ステータス</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($m = mysqli_fetch_assoc($res)) { ?>
        <?php
            // 同じ都道府県の会員数を出す
            $pr = $m['prefecture'];
            $cRes = mysqli_query($mysqli, "SELECT COUNT(*) AS c FROM members WHERE prefecture = '" . $pr . "' AND status = 1");
            $samePref = mysqli_fetch_assoc($cRes)['c'];
        ?>
        <tr>
            <td><?php echo $m['id']; ?></td>
            <td><a href="member_view.php?id=<?php echo $m['id']; ?>"><?php echo $m['name']; ?></a></td>
            <td><?php echo $m['email']; ?></td>
            <td><?php echo $m['prefecture']; ?></td>
            <td><?php echo $samePref; ?>人</td>
            <td><?php echo rank_label($m['rank']); ?></td>
            <td><?php echo status_label($m['status']); ?></td>
            <td>
                <a href="member_edit.php?id=<?php echo $m['id']; ?>">編集</a>
                <a href="member_delete.php?id=<?php echo $m['id']; ?>" onclick="return confirm('削除しますか？')">削除</a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<div class="pager">
    <?php for ($i = 1; $i <= $maxPage; $i++) { ?>
        <?php if ($i == $page) { ?>
            <span class="current"><?php echo $i; ?></span>
        <?php } else { ?>
            <a href="index.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php } ?>
    <?php } ?>
</div>
<?php
render_footer();
