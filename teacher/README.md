# teacher/（指導者用・解答）

> ⚠️ **受講生はこのフォルダを見ないでください。**
> ここには全バグの原因・修正コード・リファクタリング模範解答・レビュー観点をまとめています。

## 目次

| ファイル | 内容 |
|----------|------|
| [bug-list.md](bug-list.md) | バグ16件の一覧・症状・原因・修正コード（Issue との対応表つき） |
| [refactoring.md](refactoring.md) | リファクタリング模範解答（Service / FormRequest / 定数化 / N+1解消） |
| [review-points.md](review-points.md) | レビュー観点・レガシーポイント一覧・採点の目安 |

## 進行の目安

1. まず受講生に [docs/ISSUES.md](../docs/ISSUES.md) を再現させる（調査力）
2. 1 Issue = 1 ブランチ = 1 PR で修正させる（Git運用）
3. [docs/PULL_REQUESTS.md](../docs/PULL_REQUESTS.md) のレビュー観点でレビューする
4. バグ修正が一通り終わったら [refactoring.md](refactoring.md) の観点でリファクタリングへ

## 難易度の目安

|易しい|中|難しい|
|---|---|---|
|#15 CSV文字化け<br>#27 生年月日<br>#21 enctype<br>#34 改行表示|#12 検索<br>#18 削除<br>#21 fillable<br>#30 メール<br>#14 ソート|#33 N+1・appends<br>#23 バリデーション設計<br>#7 セキュリティ<br>#35 トランザクション

※ #34・#35 はレビュー（kishida）指摘で追加。
