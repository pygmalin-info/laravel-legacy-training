# 移行タスク（素PHP → Laravel）

上から順に進めます。**1タスク = 1ブランチ = 1PR** を推奨。
各タスクの「完了条件」を満たせば次へ。詰まったら [MAPPING.md](MAPPING.md) を参照。

> 目標は「素PHP版と同じ動作をする、**正しく動く**Laravel版」を作ること。
> `../laravel-legacy` は構造の見本。ただしバグは移植しない。

---

## STEP 0. プロジェクト準備
- `php-to-laravel/app-laravel/` に Laravel 8 プロジェクトを作成（`composer create-project laravel/laravel:^8.0 app-laravel`）
- DB（MySQL）を用意（`../laravel-legacy` の Docker をコピーして使ってもよい）
- `.env` の DB 設定、`php artisan migrate` が通る状態にする

**完了条件**: `php artisan serve` で初期画面が出る。

## STEP 1. データベース（マイグレーション & シーダー）
- `sql/init.sql` の `members` / `users` を **マイグレーション**に書き換える
- 初期データを **シーダー**（Factory 併用可）に移す
- `php artisan migrate --seed` が通ること

**完了条件**: `members` に初期データが入る。

## STEP 2. 認証（ログイン）
- `users` を使った Laravel 認証を用意（`Auth::attempt()`）
- 素PHPは md5 だったが、**Laravel は Hash（bcrypt）**にする（シーダーで `Hash::make('password')`）
- 未ログインは `login` へ（`auth` ミドルウェア）

**完了条件**: admin でログイン/ログアウトできる。SQLインジェクションでログインできないこと。

## STEP 3. 会員一覧（ルート/コントローラ/Blade）
- `index.php` を `MemberController@index` + `members/index.blade.php` に移す
- Eloquent で一覧取得 → `paginate()`
- 表示は Blade の `{{ }}` でエスケープ

**完了条件**: 一覧が表示され、ページングが動く。

## STEP 4. 検索・並び替え
- キーワード/ステータス/都道府県での絞り込みを Eloquent で（`where` はバインドされる）
- 並び替えは**許可カラムのホワイトリスト**で（ORDER BY にユーザー入力を直接入れない）

**完了条件**: 検索・並び替えが正しく動く（退会者が混ざらない）。

## STEP 5. 登録・編集・削除
- `member_add/edit/delete.php` を各アクション + Blade フォームに
- 削除は POST + `@method('DELETE')`
- 画像アップロードは `enctype` を忘れず、`Storage` で保存

**完了条件**: 登録・編集・削除・画像アップロードが動く。

## STEP 6. バリデーション
- 入力チェックを **FormRequest** に切り出す（登録・編集で共通化）
- メールの重複チェック（`unique`、編集は自分を除外）

**完了条件**: 不正入力・重複メールが弾かれる。

## STEP 7. CSV・メール
- CSV出力を実装（**BOM付き**で Excel 文字化けを防ぐ）
- 登録完了メールを `Mail` で送信（宛先・From は config/.env）

**完了条件**: CSVが文字化けせず開ける。登録時にメールが届く（MailHog）。

## STEP 8. 仕上げ（振り返り）
- マジックナンバー（status/rank）の定数化
- N+1 の解消（`withCount` など）
- 素PHP版と比べて「安全になった点・短くなった点・見通しが良くなった点」を PR にまとめる

**完了条件**: 全機能が素PHP版と同等に動き、SQLi/XSS の心配が無い状態。

---

### 進め方の目安（順番の理由）
DB → 認証 → 表示 → 入力、の順で「土台から上へ」。表示より先にDBと認証を固めると、以降のタスクで実データを見ながら進められる。
