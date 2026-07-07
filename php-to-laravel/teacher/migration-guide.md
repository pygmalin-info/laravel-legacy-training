# 移行モデル解答（素PHP → Laravel）

各 STEP の“正解の型”。`../../laravel-legacy` の該当ファイルも参照（ただしあちらはバグ入りなので、ここでは正しい形を示す）。

---

## STEP 1. マイグレーション & シーダー

素PHPの `sql/init.sql` を移す。

```php
// database/migrations/xxxx_create_members_table.php
Schema::create('members', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('name_kana')->nullable();
    $table->string('email')->index();
    $table->string('phone', 20)->nullable();
    $table->unsignedTinyInteger('gender')->default(3);
    $table->date('birthday')->nullable();
    $table->string('postal_code', 8)->nullable();
    $table->string('prefecture', 20)->nullable();
    $table->string('address')->nullable();
    $table->unsignedTinyInteger('rank')->default(1);
    $table->unsignedTinyInteger('status')->default(1);
    $table->text('memo')->nullable();
    $table->string('avatar')->nullable();
    $table->timestamp('last_login_at')->nullable();
    $table->timestamps();
});
```
- `users` はデフォルトの users マイグレーションを流用。
- シーダーで admin を `Hash::make('password')` で作成（**md5 からの脱却**）。会員は Factory で。

## STEP 2. 認証

素PHPの自前 SQL 認証 → Laravel Auth。

```php
// LoginController
public function login(Request $request) {
    $cred = $request->validate(['email' => 'required|email', 'password' => 'required']);
    if (Auth::attempt($cred)) {
        $request->session()->regenerate();
        return redirect('/members');
    }
    return back()->with('error', 'メールアドレスまたはパスワードが違います。');
}
```
- `Auth::attempt` はパスワードを Hash で照合。**SQLi の入り込む余地が無い**（バインド）。
- 保護は `Route::middleware('auth')->group(...)`。素PHPの `require_login()` の置き換え。

## STEP 3-4. 一覧・検索・並び替え

```php
public function index(Request $request) {
    $query = Member::query();

    if ($request->filled('keyword')) {
        $kw = $request->keyword;
        $query->where(function ($q) use ($kw) {      // ← OR を必ずグルーピング
            $q->where('name', 'like', "%{$kw}%")
              ->orWhere('name_kana', 'like', "%{$kw}%")
              ->orWhere('email', 'like', "%{$kw}%");
        });
    }
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    } else {
        $query->where('status', '<>', 2);
    }
    if ($request->filled('pref')) {
        $query->where('prefecture', $request->pref);
    }

    // 並び替えはホワイトリスト
    $allowed = ['id', 'name', 'rank', 'created_at'];
    $sort = in_array($request->sort, $allowed) ? $request->sort : 'id';
    $order = $request->order === 'asc' ? 'asc' : 'desc';

    $members = $query->orderBy($sort, $order)->paginate(20)->appends($request->query());
    return view('members.index', compact('members'));
}
```
- `where('...','like',...)` は**自動でバインド** → SQLi 消滅。
- `->appends()` でページ送り時も検索条件維持。
- 同県会員数（N+1）は集計1クエリ or `withCount` で。

## STEP 5. 登録・編集・削除

- Blade フォームは `@csrf`。画像は `enctype="multipart/form-data"`。
- 削除は POST + `@method('DELETE')`。
```blade
<form method="post" action="{{ route('members.destroy', $m->id) }}">
  @csrf @method('DELETE')
  <button onclick="return confirm('削除しますか？')">削除</button>
</form>
```
- 保存は `$member->fill($validated)->save()`（mass assign は `$fillable` を正しく）。
- 画像は `Storage::disk('public')->put(...)` +  `php artisan storage:link`。

## STEP 6. バリデーション（FormRequest）

```php
class MemberRequest extends FormRequest {
    public function rules() {
        $id = $this->route('id');
        return [
            'name'  => 'required|max:50',
            'email' => 'required|email|unique:members,email,'.$id,   // 編集は自分を除外
            'gender'=> 'required|in:1,2,3',
            'avatar'=> 'nullable|image|max:2048',
        ];
    }
}
```
- 登録・編集で同じ FormRequest を使う（素PHPは編集にチェックが無かった → ここで解消）。

## STEP 7. CSV・メール

- CSV: 先頭に BOM（`"\xEF\xBB\xBF"`）を付ける or `SJIS-win` 変換。`response()->streamDownload()` + `fputcsv` が堅い。
- メール: `Mail::to($member->email)->send(...)`。宛先/From は `config`/`.env`。**条件（status==1）で送らない**などの素PHPのバグは持ち込まない。

## STEP 8. 仕上げ

- status/rank を定数 or Enum に。
- N+1 は集計クエリ/`withCount` へ。
- 素PHP版との比較を PR にまとめさせる（例：「login.php の SQL 認証（SQLi あり・20行）→ `Auth::attempt` 1行、md5→bcrypt」）。

---

## 移行で必ず伝えたい“気づき”

| 素PHPで手当てが必要だったもの | Laravelでの結果 |
|------|------|
| 全SQLのプレースホルダ化 | Eloquent で最初から安全 |
| 全出力のエスケープ | Blade `{{ }}` で自動 |
| CSRFトークンの手実装 | `@csrf` で標準 |
| md5→password_hash 移行 | Auth が bcrypt で標準 |
| ページャの条件引き継ぎ | `paginate()->appends()` |

「フレームワークは“正しいやり方”をデフォルトにする道具」だと体感させるのがゴール。
