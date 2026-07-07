# 会員管理システム（素PHP版 / レガシー改修 研修教材）

フレームワークを使わない **素の PHP（PHP 8 + mysqli）** で書かれた会員管理システムです。
5〜8年ほど運用されてきた想定の、**あえて「良くない実装」を含んだ**保守練習用プロジェクトです。

「フレームワーク導入以前の、現場でよくある PHP コードを調査・改修する」体験が目的です。
（Laravel 版の教材と**同じ会員管理システム**なので、[../laravel-legacy](../laravel-legacy) と読み比べると学びが深まります）

---

## 機能

- ログイン / ログアウト
- 会員一覧 / 検索 / 並び替え / ページング
- 会員登録 / 編集 / 削除
- CSV出力
- メール送信

---

## 動作環境（Docker）

- Docker / Docker Compose

| 用途 | バージョン | コンテナ |
|------|-----------|----------|
| Web  | PHP 8.0 + Apache | `php_legacy_web` |
| DB   | MySQL 8.0 | `php_legacy_db` |
| メール確認 | MailHog | `php_legacy_mailhog` |

> Laravel 版と同時に動かせるよう、ポートをずらしています。

---

## セットアップ

```bash
docker compose up -d --build
```

- DB は初回起動時に [sql/init.sql](sql/init.sql) が自動実行され、テーブル作成＋初期データ投入されます。
- アプリ: http://localhost:8080
- メール確認（MailHog）: http://localhost:8026

### ログイン

| メールアドレス | パスワード |
|----------------|-----------|
| admin@example.com | password |

DB を作り直したいとき:
```bash
docker compose down -v && docker compose up -d --build
```

---

## この教材の進め方

1. **原因調査** — [docs/ISSUES.md](docs/ISSUES.md) の不具合を再現し、原因を特定する
2. **バグ修正** — 1 Issue = 1 ブランチ で修正する
3. **改善** — セキュリティ・可読性・重複などを直す
4. **PR作成 → レビュー対応 → マージ**

> ⚠️ このコードには意図的に多くの問題（バグ・脆弱性・悪い設計）が含まれています。
> 特に **SQLインジェクション** と **XSS** は素PHPで頻出する重大な問題です。「なぜ危険か」を説明できることを目指しましょう。

---

## ディレクトリ

```
public/          … 画面（各機能が1ファイル）。Webのドキュメントルート
  index.php        一覧・検索
  login.php / logout.php
  member_add.php / member_edit.php / member_delete.php / member_view.php
  csv.php / mail.php
inc/             … 共通（DB接続・設定・関数）。Web非公開
  config.php / db.php / functions.php
sql/init.sql     … スキーマ＋初期データ
docs/            … Issue
teacher/         … 解答（受講生は見ないこと）
```

## 修正しなくてよいもの

- **DB設計 / `sql/init.sql` のテーブル定義** … 概ね妥当。基本的に変更不要です。
