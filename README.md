# 会員管理システム（レガシー保守 研修教材）

Laravel 8 で作られた社内向けの会員管理システムです。
5〜8年ほど運用されてきた想定の、**あえて「良くない設計」を含んだ**保守練習用プロジェクトです。

新規開発ではなく、
**「既存システムを調査 → 修正 → 改善する」** という実務に近い流れを体験することが目的です。

---

## 機能

- ログイン
- 会員一覧 / 検索
- 会員登録 / 編集 / 削除
- CSV出力
- メール送信

---

## 動作環境

- PHP 8.0 以上
- MySQL 5.7 / 8.0
- Composer
- （メール確認用に MailHog があると便利です）

---

## セットアップ

```bash
composer install
cp .env.example .env
php artisan key:generate

# .env の DB 設定を自分の環境に合わせてください
php artisan migrate
php artisan db:seed

php artisan serve
```

ブラウザで http://localhost:8000 を開きます。

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
