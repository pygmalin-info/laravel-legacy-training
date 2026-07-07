# 素PHP → Laravel 移行 研修教材

**フレームワーク無しの素PHPアプリを、Laravel に載せ替える**移行案件を体験する教材です。
実務でよくある「レガシーPHPを Laravel でリプレイス」の縮図になっています。

- **移行元（Before）**: [../plain-php-legacy](../plain-php-legacy) … 素PHPの会員管理システム
- **移行先の型（参考）**: [../laravel-legacy](../laravel-legacy) … 同じ会員管理システムの Laravel 実装。**「Laravel だとどう書くか」の構造見本**として参照する

> ⚠️ `laravel-legacy` は別教材のため**意図的なバグを含みます**。移行のゴールは「あの構造を参考に、**正しく動く**Laravel版を自分で作る」ことです。バグまで移植しないこと。

---

## この教材のゴール

素PHP版と**同じ機能・同じ画面**を、Laravel の作法で作り直す：

- ルーティング（`routes/web.php`）
- コントローラ（`MemberController`）
- Eloquent モデル（`Member` / `User`）
- Blade テンプレート
- マイグレーション / シーダー
- 認証（Laravel の Auth）
- バリデーション（FormRequest）

そして移行の過程で、素PHPにあった**セキュリティ問題（SQLi/XSS）や重複コードが、Laravel では標準機能で解消される**ことを体感します。

---

## 進め方

1. [docs/TASKS.md](docs/TASKS.md) の移行タスクを上から順に進める（1タスク=1ブランチ=1PR 推奨）
2. 迷ったら [docs/MAPPING.md](docs/MAPPING.md)（素PHP ↔ Laravel 対応表）を見る
3. 各タスクの完了条件（画面が同じ動作をするか）を満たすことを確認
4. すべて移行できたら、素PHP版との差（安全性・行数・見通し）を振り返る

## 前提知識

Laravel の基本（Routing / Controller / Model / Blade / Migration / Validation / Eloquent / Auth）を学習済みであること。
未学習なら先に [../laravel-legacy](../laravel-legacy) を写経・改修してから取り組むとよい。

## 成果物の置き場

`php-to-laravel/app-laravel/`（自分で `laravel new` して作る）に構築する想定です。
このフォルダ自体には課題（docs）と解答（teacher）だけを置いています。
