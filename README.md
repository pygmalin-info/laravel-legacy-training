# PHP/Laravel レガシー保守・移行 研修教材シリーズ

会員管理システムを題材にした、**実務に近い「調査・改修・移行」**を体験する研修教材集です。
3つの教材で構成され、どれも**同じ会員管理システム**を扱うので、読み比べ・移行課題として繋がります。

| 教材 | 内容 | 主に学ぶこと |
|------|------|-------------|
| [laravel-legacy/](laravel-legacy/) | Laravel 8 のレガシー保守 | Fat Controller・N+1・意図的バグ14個の調査と改修 |
| [plain-php-legacy/](plain-php-legacy/) | 素PHP（FW無し）のレガシー改修 | SQLインジェクション・XSS など**セキュリティ**中心の改修 |
| [php-to-laravel/](php-to-laravel/) | 素PHP → Laravel 移行 | レガシーPHPを Laravel に載せ替える移行案件 |

## どれから？

- **Laravel は学習済み → 改修から** … `laravel-legacy/`
- **素PHPの怖さ（セキュリティ）を知りたい** … `plain-php-legacy/`
- **移行案件を体験したい** … `php-to-laravel/`（`plain-php-legacy` を移行元に使う）

## 各教材の起動

それぞれのフォルダに `README.md` と Docker 一式があります（ポートは重複しないよう分離）。

| 教材 | アプリ | メール確認 |
|------|--------|-----------|
| laravel-legacy | http://localhost:8000 | http://localhost:8025 |
| plain-php-legacy | http://localhost:8080 | http://localhost:8026 |

```bash
cd laravel-legacy && docker compose up -d      # Laravel版
cd plain-php-legacy && docker compose up -d     # 素PHP版
```

共通ログイン: `admin@example.com` / `password`

## 解答について

各教材の `teacher/` に解答（バグ一覧・原因・修正コード・レビュー観点）を置いています。
**受講生には見せないでください。**
