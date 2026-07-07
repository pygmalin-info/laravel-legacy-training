# teacher/（指導者用・解答）／PHP→Laravel 移行

> ⚠️ **受講生はこのフォルダを見ないでください。**

| ファイル | 内容 |
|----------|------|
| [migration-guide.md](migration-guide.md) | 各 STEP のモデル解答（コード例つき） |
| [checklist.md](checklist.md) | レビュー観点・つまずき所・採点の目安 |

## この教材のねらい

- 「素PHP → Laravel」の**移行案件**を体験する（実務で非常に多い）
- 移行の過程で、素PHPの**セキュリティ問題（SQLi/XSS）・重複・N+1 が、Laravelの標準機能で自然に解消される**ことを体感させる
- 3教材の位置づけ:
  - [../../plain-php-legacy](../../plain-php-legacy) … 移行元（素PHP・改修教材）
  - [../../laravel-legacy](../../laravel-legacy) … Laravel の構造見本（別途バグ改修教材）
  - 本教材 … 上記2つを繋ぐ「移行」課題

## 指導のポイント

- **一気に移さない**。STEP順（DB→認証→表示→入力）で少しずつ動かす。
- `laravel-legacy` を“答え”としてコピーさせない。**バグ入り**なので、構造の参考に留めさせる。
- 移行完了後に「素PHPで何行・どんな穴だった処理が、Laravelで何に化けたか」を言語化させると学びが定着する。
