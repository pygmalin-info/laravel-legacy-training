# バグ一覧・原因・修正（解答）／素PHP版

仕込んであるバグは **14件**（Issue 10件にまとめて掲載）。
Laravel 版と同じ会員管理システムだが、素PHPならではの**SQLインジェクション・XSS・md5パスワード**が加わっている。

## Issue ↔ バグ 対応表

| Issue | バグ | 場所 |
|-------|------|------|
| #12 | 1. 検索の OR グルーピング崩れ／退会者混入 | `public/index.php` |
| #14 | 11. ソートが無検証・昇降切替なし | `public/index.php` |
| #15 | 2. CSV に BOM 無しで文字化け | `public/csv.php` |
| #18 | 3. 削除できない（GETリンク × `$_POST['id']`） | `index.php` / `member_delete.php` |
| #21 | 4. 電話・カナが更新されない（UPDATE列漏れ） | `public/member_edit.php` |
| #21 | 9b. 画像アップロード不可（enctype 漏れ） | `public/member_add.php` |
| #23 | 5. メール重複登録できる（重複チェック無し） | `public/member_add.php` |
| #23 | 9a. 入力チェックが無い | `member_add.php` / `member_edit.php` |
| #27 | 12. 生年月日が1970年（`date()` に文字列） | `public/member_view.php` |
| #30 | 13. 登録メールが送られない（status条件） | `public/member_add.php` |
| #33 | 14. 一覧の N+1（ループ内 count） | `public/index.php` |
| #33 | 10. ページ送りで検索条件が消える | `public/index.php` |
| #7 | 6. SQLインジェクション（文字列連結） | `login.php` ほぼ全ページ |
| #7 | 7. XSS（未エスケープ出力） | `index.php` / `member_view.php` |
| #7 | 8. パスワードが md5（脆弱） | `login.php` / `sql/init.sql` |

---

## 6・8. SQLインジェクション / md5（#7）★最重要

**原因**: すべてのSQLがユーザー入力を文字列連結している。ログインは典型的な認証回避が可能。
```php
// login.php Before
$sql = "SELECT * FROM users WHERE email = '" . $email . "' AND password = '" . md5($pass) . "' LIMIT 1";
// email に  ' OR '1'='1' -- 　を入れると誰でもログインできる
```
**修正**: プリペアドステートメント＋`password_hash()`/`password_verify()`。
```php
// After
$stmt = mysqli_prepare($mysqli, "SELECT * FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if ($user && password_verify($pass, $user['password'])) { /* ログイン成功 */ }
```
- パスワードは登録時に `password_hash($pass, PASSWORD_DEFAULT)` で保存。既存の md5 データは移行が必要（教材では作り直しでOK）。
- **他の全ページの SQL も同様にプレースホルダへ**（`index.php` の検索・`member_add/edit/view/delete` すべて対象）。これがこの教材の一番のヤマ。

## 7. XSS（#7）
**原因**: DBの値やGET値をエスケープせず出力している。
```php
// index.php Before
<td><?php echo $m['name']; ?></td>
<?php echo '「' . $keyword . '」の検索結果'; ?>
// member_view.php Before
<td><?php echo $m['memo']; ?></td>
```
**修正**: 出力は必ず `h()`（= `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`）を通す。
```php
<td><?php echo h($m['name']); ?></td>
<?php echo '「' . h($keyword) . '」の検索結果'; ?>
<td><?php echo nl2br(h($m['memo'])); ?></td>
```
> `functions.php` に `h()` は用意済み。使えていない箇所を潰していく。

## 1. 検索がおかしい（#12）
**原因**: OR をカッコで括らずに連結しているため、`status <> 2` と優先順位が崩れる。
```php
// Before
$where .= " AND name LIKE '%$keyword%' OR name_kana LIKE '%$keyword%' OR email LIKE '%$keyword%' ";
```
**修正**: キーワード条件をカッコで括る（プレースホルダ化と併せて）。
```php
$where .= " AND (name LIKE ? OR name_kana LIKE ? OR email LIKE ?) ";
```

## 11. 並び替えがおかしい（#14）
**原因**: `ORDER BY $sort $order` に GET 値を無検証で埋めている（SQLi にもなる）。リンクも `order=asc` 固定。
**修正**: ホワイトリスト＋昇降トグル。
```php
$allowed = ['id','name','rank','created_at'];
$sort  = in_array($_GET['sort'] ?? '', $allowed) ? $_GET['sort'] : 'id';
$order = (($_GET['order'] ?? '') === 'asc') ? 'ASC' : 'DESC';
// リンク側で現在の order を見て次の向きを入れる
```

## 2. CSV文字化け（#15）
**原因**: UTF-8 のまま BOM 無しで出力 → Excel が誤認。
**修正**: 先頭に BOM を付ける（または SJIS 変換）。
```php
echo "\xEF\xBB\xBF";      // ← これを echo $out; の前に
echo $out;
// もしくは: echo mb_convert_encoding($out, 'SJIS-win', 'UTF-8');
```
> さらに堅くするなら `fputcsv()` でカンマ・改行をエスケープする。

## 3. 削除できない（#18）
**原因**: `member_delete.php` は `$_POST['id']` を見ているが、一覧の削除は GET リンク（`?id=`）。POST に id が無いので何も消えない。
**修正**: 削除は POST フォームにする（誤削除・CSRF対策にもなる）。
```php
// index.php（リンク → フォームへ）
<form method="post" action="member_delete.php" style="display:inline"
      onsubmit="return confirm('削除しますか？')">
  <input type="hidden" name="id" value="<?php echo (int)$m['id']; ?>">
  <button type="submit">削除</button>
</form>
```

## 4. 電話・カナが更新されない（#21）
**原因**: `member_edit.php` の UPDATE 文に `phone` と `name_kana` の SET が無い。
**修正**: SET 句に追加（プレースホルダで）。
```php
UPDATE members SET name=?, name_kana=?, email=?, phone=?, gender=?, ... WHERE id=?
```

## 9b. 画像アップロード不可（#21）
**原因**: 登録フォームに `enctype="multipart/form-data"` が無く、ファイルが送られない。
**修正**:
```php
<form method="post" action="member_add.php" enctype="multipart/form-data">
```
（`move_uploaded_file` 側は正しい。拡張子・サイズの検証を足すとなお良い）

## 5・9a. 重複登録／入力チェック無し（#23）
**原因**: 登録・編集ともにチェックが無い。
**修正**: 保存前にバリデーションと重複チェックを行う。
```php
$errors = [];
if (trim($name) === '') $errors[] = '氏名は必須です';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'メール形式が不正です';
// 重複チェック（編集時は自分のIDを除外）
$stmt = mysqli_prepare($mysqli, "SELECT id FROM members WHERE email = ?");
...
if ($dup) $errors[] = 'このメールは既に登録されています';
if ($errors) { /* エラー表示して再入力 */ }
```

## 12. 生年月日1970（#27）
**原因**: `date('Y年n月j日', (int)$m['birthday'])` の第2引数は UNIXタイムスタンプ(int)。`(int)'1990-04-01'` が `1990` になり、date は 1970年起点＋1990秒 ≒ 1970年を表示してしまう。
（※ 素の `date('...', $m['birthday'])` は PHP 8 では TypeError で落ちるため、当時のコードは `(int)` キャストで“動いてしまっている”状態）
**修正**:
```php
echo date('Y年n月j日', strtotime($m['birthday']));
// または: echo (new DateTime($m['birthday']))->format('Y年n月j日');
```

## 13. メールが送られない（#30）
**原因**: `member_add.php` は `if ($status == 1)` の中で送信しているが、登録直後は `$status = 0`（仮登録）なので常に送られない。
**修正**: 条件を外して登録時に送る（または有効化フローで送る）。ハードコードの From/Bcc は `config.php` に集約済みなのでそれを使う。

## 14. 一覧の N+1（#33）
**原因**: 一覧のループ内で、行ごとに「同県会員数」の COUNT クエリを発行している。
**修正**: 都道府県ごとの件数を1クエリで集計し、連想配列で引く。
```php
$counts = [];
$r = mysqli_query($mysqli, "SELECT prefecture, COUNT(*) c FROM members WHERE status=1 GROUP BY prefecture");
while ($row = mysqli_fetch_assoc($r)) { $counts[$row['prefecture']] = $row['c']; }
// ループ内: $samePref = $counts[$m['prefecture']] ?? 0;
```

## 10. ページ送りで検索条件が消える（#33）
**原因**: ページャのリンクが `index.php?page=N` だけで、検索条件（keyword等）を引き継いでいない。
**修正**: 現在のGET条件をリンクに引き継ぐ。
```php
$qs = $_GET; unset($qs['page']);
$base = http_build_query($qs);
// リンク: index.php?<?php echo $base; ?>&page=<?php echo $i; ?>
```
