# 会員管理システム（レガシー保守 研修教材）

Laravel8で作られた社内向けの会員管理システムです。
5〜8年ほど運用されてきた想定の、**あえて「良くない設計」を含んだ**保守練習用プロジェクトです。新規開発ではなく、**「既存システムを調査 → 修正 → 改善する」** という実務に近い流れを体験することが目的です。

---

## 機能

- ログイン
- 会員一覧 / 検索
- 会員登録 / 編集 / 削除
- CSV出力
- メール送信

---

## 動作環境

**Docker で全員同じ環境になります**（PHP・MySQL・メールのバージョンを揃えるため）。

- Docker / Docker Compose（Docker Desktop でOK）

> ローカルに PHP や MySQL を入れる必要はありません。中身のバージョンは以下で固定です。
>
> | 用途 | バージョン | コンテナ |
> |------|-----------|----------|
> | PHP  | 8.0 (fpm) | `app` |
> | Web  | Nginx 1.25 | `web` |
> | DB   | MySQL 8.0 | `db` |
> | メール確認 | MailHog | `mailhog` |

---

## セットアップ（Docker）

```bash
# 1. コンテナを起動（初回はイメージのビルドが走ります）
docker compose up -d --build

# 2. .env を用意
cp .env.example .env

# 3. 依存パッケージのインストール〜初期化（すべて app コンテナ内で実行）
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

- アプリ: http://localhost:8000
- 送信メールの確認（MailHog 受信箱）: http://localhost:8025

### よく使うコマンド

```bash
docker compose exec app php artisan ...   # artisan コマンド
docker compose exec app composer ...      # composer コマンド
docker compose exec app bash              # コンテナに入る
docker compose down                       # 停止
docker compose down -v                    # 停止＋DBデータも削除（作り直したいとき）
```

> `.env` の DB 接続先は Docker 用に `DB_HOST=db` / `member` / `secret` で設定済みです。
> DB を初期状態に戻したいときは `docker compose exec app php artisan migrate:fresh --seed`。

<details>
<summary>Docker を使わない場合（任意）</summary>

PHP 8.0 / MySQL / Composer が手元にある人向け。`.env` の `DB_HOST` を `127.0.0.1` などに変更してください。

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```
</details>

### ログイン

| メールアドレス | パスワード |
|----------------|-----------|
| admin@example.com | password |

---

## この教材の進め方

1. **原因調査** — [docs/ISSUES.md](docs/ISSUES.md) の不具合を再現し、原因を特定する
2. **バグ修正** — 1 Issue = 1 ブランチ で修正する
3. **リファクタリング** — [docs/PULL_REQUESTS.md](docs/PULL_REQUESTS.md) のレビュー観点を参考に改善する
4. **PR作成** — 修正内容と確認手順を書いて Pull Request を出す
5. **レビュー対応** — レビューコメントに返信・修正する
6. **マージ**

> ⚠️ このコードには意図的に多くの問題（バグ・悪い設計）が含まれています。
> 「なぜ動かないのか」「なぜ危険なのか」を自分の言葉で説明できることを目指しましょう。

---

## 修正しなくてよいもの

- **DB設計 / マイグレーション** … 概ね妥当です。基本的に変更不要です。
- **Laravel 標準の枠組み**（Kernel, Provider など）… 触る必要はありません。

---

## ディレクトリ

```
app/Http/Controllers/MemberController.php  … 中心。かなり長い（Fat Controller）
app/Models/Member.php
resources/views/members/                    … 一覧・登録・編集・詳細
database/seeders/                           … 動作確認用データ
docs/                                        … Issue と PR レビュー
teacher/                                     … 解答（受講生は見ないこと）
```
