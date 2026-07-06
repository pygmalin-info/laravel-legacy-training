# バグ一覧・原因・修正（解答）

仕込んであるバグは全部で **14件**（Issue 10件にまとめて掲載）。
対応表 → 各バグの「症状 / 原因 / 修正」の順にまとめています。

## Issue ↔ バグ 対応表

| Issue | バグ | 場所 |
|-------|------|------|
| #12 | 1. 検索の OR グルーピング崩れ／退会者混入 | `MemberController@index` |
| #14 | 11. ソートの whitelist なし・昇降切替なし | `MemberController@index` / `index.blade.php` |
| #15 | 2. CSV に BOM が無く Excel で文字化け | `MemberController@csv` |
| #18 | 3. 削除が GET リンク（DELETE ルート不一致） | `index.blade.php` / `show.blade.php` |
| #21 | 4. 電話・カナが更新されない（$fillable 漏れ） | `Member` / `MemberController@update` |
| #21 | 9. 画像アップロード不可（enctype 漏れ） | `create.blade.php` |
| #23 | 5. メール重複登録できる（unique 無し） | `MemberController@store` |
| #23 | 8. 編集時にバリデーションが無い | `MemberController@update` |
| #27 | 12. 生年月日が 1970年（date に文字列） | `show.blade.php` |
| #30 | 13. 登録メールが送られない（status 条件） | `MemberController@store` |
| #33 | 14. 一覧の N+1（ループ内 count） | `MemberController@index` |
| #33 | 10. ページ送りで検索条件が消える（appends 漏れ） | `index.blade.php` |
| #7 | 6. SQLインジェクション（Raw SQL 連結） | `MemberController@index` |
| #7 | 7. XSS（`{!! !!}` で memo 出力） | `show.blade.php` |

---

## 1. 検索結果がおかしい（#12）

**症状**: 氏名検索で退会者が混ざる。ステータス絞り込みが効かない。

**原因**: `orWhere` をグルーピングせずに繋いでいるため、後から足した `where('status', ...)` と OR/AND の優先順位が崩れる。
SQL は実質 `... name like ? OR kana like ? OR (email like ? AND status <> 2)` になり、名前が一致する退会者も出てしまう。

```php
// Before
if ($keyword != '') {
    $query->where('name', 'like', '%'.$keyword.'%')
          ->orWhere('name_kana', 'like', '%'.$keyword.'%')
          ->orWhere('email', 'like', '%'.$keyword.'%');
}
```

**修正**: キーワード条件をクロージャで 1 グループに閉じる。

```php
// After
if ($keyword != '') {
    $query->where(function ($q) use ($keyword) {
        $q->where('name', 'like', '%'.$keyword.'%')
          ->orWhere('name_kana', 'like', '%'.$keyword.'%')
          ->orWhere('email', 'like', '%'.$keyword.'%');
    });
}
```

---

## 2. CSV が文字化けする（#15）

**症状**: Excel で開くと文字化け。

**原因**: 出力が UTF-8 のみで **BOM が無い**ため、Excel が Shift_JIS と誤認する。

```php
// After（どちらかでよい）
// (A) UTF-8 のまま BOM を付ける
$csv = "\xEF\xBB\xBF" . $csv;

// (B) Shift_JIS に変換する
$csv = mb_convert_encoding($csv, 'SJIS-win', 'UTF-8');
```

> 補足: カンマや改行を含む値のために `fputcsv` を使うのがより堅い（refactoring.md 参照）。

---

## 3. 削除できない（#18）

**症状**: 「削除」を押しても消えず、詳細が開く。

**原因**: ルートは `Route::delete(...)` なのに、ビューが **GET の `<a href>`** で叩いている。GET でアクセスされるため `show` に流れる（または 405）。

```blade
{{-- Before --}}
<a href="/members/{{ $m->id }}" onclick="return confirm('削除しますか？')">削除</a>
```

**修正**: DELETE を送るフォームにする。

```blade
{{-- After --}}
<form action="/members/{{ $m->id }}" method="post" onsubmit="return confirm('削除しますか？')" style="display:inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger">削除</button>
</form>
```

---

## 4. 電話番号・カナが更新されない（#21）

**症状**: 編集で電話・カナを変えても保存されない。他項目は反映される。

**原因**: `update()` が `$member->update($request->all())` を使っており、`Member::$fillable` に **`phone` と `name_kana` が入っていない**ため mass assignment で弾かれる。

```php
// Member.php Before
protected $fillable = ['name','email','gender','birthday','postal_code','prefecture','address','rank','status','memo','avatar'];
```

**修正**: fillable に不足項目を追加する（本来はバリデーション済みデータで更新するのが望ましい → refactoring.md）。

```php
// After
protected $fillable = ['name','name_kana','email','phone','gender','birthday','postal_code','prefecture','address','rank','status','memo','avatar'];
```

---

## 5. 重複登録できる（#23）

**症状**: 登録済みメールで再登録できる。

**原因**: `store()` のバリデーションに `unique` が無い。DB にも unique 制約が無い（マイグレーションの方針上、アプリ側で担保する前提）。

```php
// After
$request->validate([
    'name'  => 'required|max:50',
    'email' => 'required|email|unique:members,email',
    'gender'=> 'required',
]);
```

> マイグレーションは変更不要。アプリ側バリデーションで担保する。編集時は `unique:members,email,{$id}` で自分自身を除外する。

---

## 6. SQLインジェクション（#7）

**症状**: 電話番号検索の入力がそのまま SQL に入る。

**原因**: `DB::select` に文字列連結でユーザー入力を埋め込んでいる。

```php
// Before
$tmp = DB::select("select id from members where phone like '%".$request->phone."%'");
```

**修正**: プレースホルダ（バインド）を使う。そもそも Eloquent に寄せるのが一番安全。

```php
// After (A) バインド
$rows = DB::select('select id from members where phone like ?', ['%'.$request->phone.'%']);

// After (B) Eloquent に統一（推奨）
if ($request->phone != '') {
    $query->where('phone', 'like', '%'.$request->phone.'%');
}
```

---

## 7. XSS（#7）

**症状**: メモに `<script>` を入れると詳細画面で実行される。

**原因**: Blade で `{!! !!}`（エスケープなし出力）を使っている。

```blade
{{-- Before --}}
<td>{!! $member->memo !!}</td>
{{-- After --}}
<td>{{ $member->memo }}</td>
```

改行を活かしたいなら `{{ }}` + `nl2br(e(...))`:
```blade
<td>{!! nl2br(e($member->memo)) !!}</td>
```

---

## 8. 編集時にバリデーションが無い（#23）

**症状**: 編集画面では空氏名などでも保存できる。

**原因**: `update()` にバリデーションが一切無く、`$request->all()` を丸ごと保存している。

**修正**: 登録と同じルールを効かせる（重複は自分を除外）。FormRequest に共通化するのがベスト（refactoring.md）。

```php
public function update(Request $request, $id)
{
    $member = Member::findOrFail($id);
    $validated = $request->validate([
        'name'  => 'required|max:50',
        'email' => 'required|email|unique:members,email,'.$member->id,
        'gender'=> 'required',
    ]);
    $member->fill($validated);
    $member->phone = $request->phone;
    $member->name_kana = $request->name_kana;
    // ... 必要な項目を明示的に
    $member->save();
    return redirect('/members/'.$id)->with('message', '会員情報を更新しました');
}
```

---

## 9. 画像アップロードできない（#21）

**症状**: 画像を選んでも保存されない。

**原因**: 登録フォームに `enctype="multipart/form-data"` が無く、ファイルがサーバに届かない。

```blade
{{-- Before --}}
<form action="/members" method="post">
{{-- After --}}
<form action="/members" method="post" enctype="multipart/form-data">
```

> Controller 側の `hasFile`→`move` 処理は正しい。フォーム属性だけの問題。
> 余裕があれば `storage` + `php artisan storage:link` 運用や、拡張子・サイズのバリデーション（`image|mimes:jpg,png|max:2048`）も指導する。

---

## 10. ページ送りで検索条件が消える（#33）

**症状**: 検索・並び替え後に 2ページ目へ行くと条件が消える。

**原因**: `paginate()` のページリンクは `page` しか引き継がない。検索条件を `appends` していない。

```blade
{{-- Before --}}
{{ $members->links() }}
{{-- After --}}
{{ $members->appends(request()->query())->links() }}
```

---

## 11. 並び替えがおかしい（#14）

**症状**: 昇順・降順が切り替わらない。存在しない列でエラーの懸念。

**原因**: `orderBy($request->sort, $request->order)` を無検証で渡している。`order` が空だと不正、列名も無制限（任意カラム指定＝情報漏えいリスク）。ビューのリンクも常に `order=asc` 固定。

**修正**: 許可列のホワイトリスト＋昇降トグル。

```php
// Controller
$allowed = ['id', 'name', 'rank', 'created_at'];
$sort  = in_array($request->sort, $allowed) ? $request->sort : 'id';
$order = $request->order === 'asc' ? 'asc' : 'desc';
$query->orderBy($sort, $order);
```

```blade
{{-- View: クリックのたびに昇降を反転 --}}
@php $next = (request('sort')==='name' && request('order')==='asc') ? 'desc' : 'asc'; @endphp
<th><a href="{{ route('members.index', array_merge(request()->query(), ['sort'=>'name','order'=>$next])) }}">氏名</a></th>
```

---

## 12. 生年月日が 1970年（#27）

**症状**: 生年月日が「1970年1月1日」になる。

**原因**: `date('Y年n月j日', $member->birthday)` の第2引数は **UNIXタイムスタンプ（int）** を期待するが、`birthday` は `"1990-04-01"` という文字列。int に暗黙変換され `1990`（≒1970年）になる。

```blade
{{-- Before --}}
<td>{{ date('Y年n月j日', $member->birthday) }}</td>
{{-- After --}}
<td>{{ \Carbon\Carbon::parse($member->birthday)->format('Y年n月j日') }}</td>
```

より良い解: モデルで casts する。
```php
// Member.php
protected $casts = ['birthday' => 'date'];
// → view では {{ $member->birthday->format('Y年n月j日') }}
```

---

## 13. 登録メールが届かない（#30）

**症状**: 会員登録しても完了メールが送られない。

**原因**: `store()` はメール送信を `if ($member->status == 1)` で囲っているが、登録直後は `status = 0`（仮登録）にしているため **条件が常に偽**。

```php
// Before
$member->status = 0;
...
if ($member->status == 1) {
    Mail::send(...);
}
```

**修正**: 仕様に合わせて条件を外す（登録時に送る）か、status を有効化してから送る。学習用には条件を外すのが素直。

```php
// After
Mail::send('emails.notification', $data, function ($mail) use ($member) {
    $mail->to($member->email);
    $mail->subject('会員登録が完了しました');
});
```

> ハードコードの `from` / `bcc` は config・.env へ（refactoring.md）。

---

## 14. 一覧の N+1（#33）

**症状**: 一覧表示だけで大量の SQL。会員が増えると遅い。

**原因**: 一覧取得後、**ループ内で毎回 `count()` クエリ**を発行している（20件表示なら +20 クエリ）。

```php
// Before
foreach ($members as $m) {
    $cnt = DB::table('members')->where('prefecture', $m->prefecture)->where('status', 1)->count();
    $m->same_pref_count = $cnt;
}
```

**修正**: 都道府県ごとの件数を **1クエリ**で集計してマッピングする。

```php
// After
$counts = DB::table('members')
    ->select('prefecture', DB::raw('count(*) as cnt'))
    ->where('status', 1)
    ->groupBy('prefecture')
    ->pluck('cnt', 'prefecture');

foreach ($members as $m) {
    $m->same_pref_count = $counts[$m->prefecture] ?? 0;
}
```

> `show()` にも同種のクエリがあるが、単発なので優先度は低い。
