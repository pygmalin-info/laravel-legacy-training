# 素PHP ↔ Laravel 対応表

移行の「読み替え辞書」。素PHP版の各要素が Laravel だとどこに対応するか。

## ファイル・構造

| 素PHP版 | Laravel版 | メモ |
|---------|-----------|------|
| `public/index.php` ほか各ページ | `routes/web.php` + `MemberController` の各アクション | URL 1つ = ルート1行 |
| `inc/db.php`（mysqli接続） | `config/database.php` + `.env` | 接続は framework が管理 |
| `inc/config.php`（定数） | `.env` + `config/*.php` | ハードコードを追い出す |
| `inc/functions.php` の `h()` | Blade の `{{ }}`（自動エスケープ） | XSS対策が標準化 |
| `inc/functions.php` の `require_login()` | `auth` ミドルウェア | ルートに `->middleware('auth')` |
| 手書きの `<table>` HTML | Blade テンプレート（`resources/views/`） | ロジックと表示を分離 |
| `sql/init.sql` | `database/migrations/` + `database/seeders/` | スキーマをコード管理 |
| `mysqli_query("SELECT ...")` | Eloquent（`Member::where(...)`） | プレースホルダが標準 |
| `login.php` の自前認証 | `Auth::attempt()` | パスワードは `Hash`（bcrypt） |
| `mail()` 直呼び | `Mail::send()` / Mailable | 設定は `config/mail.php` |

## セキュリティの読み替え（重要）

| 素PHPの問題 | Laravelでの解消 |
|-------------|-----------------|
| SQLインジェクション（文字列連結） | Eloquent/クエリビルダが**自動でバインド** |
| XSS（`echo $値`） | Blade `{{ }}` が**自動エスケープ**（`{!! !!}` は要注意） |
| CSRF なし | Blade `@csrf` + VerifyCsrfToken ミドルウェア |
| パスワード md5 | `Hash::make()` / `Auth`（bcrypt） |

## データの対応

テーブル定義（`sql/init.sql`）は Laravel のマイグレーションにほぼ1:1で移せる。
`members` / `users` のカラムは [../laravel-legacy/database/migrations](../../laravel-legacy/database/migrations) と揃えてあるので参照可。

| 概念 | 素PHP | Laravel |
|------|-------|---------|
| status | `0/1/2` の数値直書き | 定数 or Enum（`app/Consts` 等） |
| rank | `1/2/3` | 同上 |
| 一覧の同県会員数（N+1） | ループ内 `COUNT` | 1クエリ集計 or `withCount` |
| ページング | 手書き `LIMIT/OFFSET` | `->paginate()` |
