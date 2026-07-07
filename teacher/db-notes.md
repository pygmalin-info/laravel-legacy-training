# DB設計メモ（指導者用）

> ⚠️ 受講生非公開。教材のDB設計に関する補足・判断の記録。

## TIMESTAMP と DATETIME（2038年問題）※山田さんの指摘によりコラムとして追加しました。

この教材の Laravel マイグレーションは `$table->timestamps()` / `$table->timestamp()` を使っており、
**MySQL では `TIMESTAMP` 型**になります（`users`・`members` の `created_at` / `updated_at` / `last_login_at` など。
`member_logs.created_at` も同様）。

### 論点
- MySQL `TIMESTAMP`：**1970-01-01 〜 2038-01-19** の範囲（4バイト）＝いわゆる **2038年問題**
- MySQL `DATETIME`：1000-01-01 〜 9999-12-31。範囲制限で困らない
- Laravel の `timestamp()` / `timestamps()` は、MySQL では `TIMESTAMP` にマップされる（`dateTime()` なら `DATETIME`）

### この教材での方針（あえて直さない）
- 監査系（`created_at` / `updated_at` / `last_login_at`）は当分 2038 に達しないため、**この教材では TIMESTAMP のまま**にしている。
  マイグレーションは「DB設計は正しい・修正不要」という教材前提を保つため、コードは変更しない。
- ただし指導時に **「実務では、未来日時（有効期限・予約日時など）を持つ列は `DATETIME` を選ぶ」** ことを必ず伝える。
  TIMESTAMPにそういう列を作ると、2038を超えた瞬間に壊れる。

### 直す場合の書き方（参考）
Laravel8には `datetimes()` ヘルパが無い（10以降）ので、DATETIME にしたいときは明示する：
```php
$table->dateTime('created_at')->nullable();
$table->dateTime('updated_at')->nullable();
$table->dateTime('last_login_at')->nullable();
```
- なお **素PHP版（`plain-php-legacy/sql/init.sql`）は最初から `DATETIME`** を使っている。Laravel版だけTIMESTAMPという違いがある点も、対比のネタになる。

### 実型の確認コマンド
```sql
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'member_management'
  AND DATA_TYPE IN ('timestamp', 'datetime')
ORDER BY TABLE_NAME, ORDINAL_POSITION;
```
（この教材の初期状態では、日時列はすべて `timestamp` で出力される）
