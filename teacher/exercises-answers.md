# 発展課題 模範解答（指導者用）

> ⚠️ 受講生非公開。[docs/EXERCISES.md](../docs/EXERCISES.md) の各課題の解答。

スカフォールド済み: `users.role`（admin/staff）、`member_logs`＋`MemberLog` モデル、E11用のバグ（create.blade の name 不一致）。

---

## E1. ポリシー（認可） 

```php
// app/Policies/MemberPolicy.php
namespace App\Policies;
use App\Models\User;
use App\Models\Member;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemberPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Member $member) { return true; }      // 編集は全員可（仕様次第）
    public function delete(User $user, Member $member) { return $user->isAdmin(); } // 削除は admin だけ
}
```
```php
// app/Providers/AuthServiceProvider.php
protected $policies = [ \App\Models\Member::class => \App\Policies\MemberPolicy::class ];
```
```php
// MemberController@destroy
$member = Member::findOrFail($id);
$this->authorize('delete', $member);   // staff は 403
$member->delete();
```
```blade
{{-- 一覧・詳細の削除ボタン --}}
@can('delete', $m)
  <form method="post" action="/members/{{ $m->id }}" ...>@csrf @method('DELETE')<button>削除</button></form>
@endcan
```
**確認**: `staff@example.com` でログイン → 削除ボタンが消える／直接叩くと 403。`admin@example.com` は従来どおり。
> `authorize()` は `AuthorizesRequests` トレイト由来（= E7 の「継承元の機能」）。

---

## E2. FormValidation（Form Request Validation）に切り出す
「FormValidation」= Laravel の **Form Request Validation**。実体は `FormRequest` クラス。
```bash
php artisan make:request MemberRequest
```
[refactoring.md](refactoring.md) の `MemberRequest` を参照。`store(MemberRequest $r)` / `update(MemberRequest $r, $id)` に置換し、`unique:members,email,{$id}` で編集時は自分を除外。
- 指導ポイント（津田さん指摘）: AIや初学者は `$request->validate([...])` を Controller に書きがちで、`FormRequest` に切り出す発想が出にくい。「フォームごとの検証はフォーム専用クラスに寄せる」を体で覚えさせる。

## E3. クエリパラメータのバリデーション
```php
public function index(Request $request)
{
    $request->validate([
        'status' => 'nullable|in:0,1,2',
        'sort'   => 'nullable|in:id,name,rank,created_at',
        'order'  => 'nullable|in:asc,desc',
        'page'   => 'nullable|integer|min:1',
    ]);
    ...
}
```
- `sort` を `in:` で縛れば、ソートの SQL インジェクション（#14/#11）も同時に塞げる。

## E4. タイプヒンティング
```php
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

public function index(Request $request): View { ... }
public function store(MemberRequest $request): RedirectResponse { ... }
public function show(int $id): View { ... }
private function sendNotification(Member $member, string $subject, string $body): void { ... }
```
- 戻り値・引数・プロパティに型を付ける。`declare(strict_types=1);` まで踏み込んでもよい。

## E5. 列挙型（enum） ※PHP8.1
**先に Docker を 8.1 へ**：`docker/php/Dockerfile` の `FROM php:8.0-fpm` を `php:8.1-fpm` に変更し、`composer.json` の `"php": "^8.0"` を `"^8.0|^8.1"` に（Laravel 8 は 8.1 で動作）。`docker compose up -d --build`。

```php
// app/Enums/MemberStatus.php
enum MemberStatus: int {
    case Pending = 0;
    case Active  = 1;
    case Left    = 2;
    public function label(): string {
        return match($this) {
            self::Pending => '仮登録',
            self::Active  => '有効',
            self::Left    => '退会',
        };
    }
}
```
```php
// Member.php
protected $casts = ['status' => MemberStatus::class, /* rank も同様に */];
// view: {{ $member->status->label() }}
```
- rank / gender も同様。定数クラスで妥協する場合は [refactoring.md](refactoring.md) の定数節。

## E6. リレーション先で絞り込む
```php
// Member.php
public function logs() { return $this->hasMany(\App\Models\MemberLog::class); }
```
```php
// 履歴のある会員だけ
$query->whereHas('logs');
// 特定アクションを持つ会員
$query->whereHas('logs', fn($q) => $q->where('action', 'updated'));
// 件数を N+1 なしで
$members = $query->withCount('logs')->paginate(20);   // $m->logs_count
// 履歴も一緒に取る（N+1回避）
$members->load('logs');
```
**確認データ**: シードで `教材 太郎`(2件)・`テスト 花子`(1件) のみ履歴あり。他は 0 件なので絞り込みが体感できる。

## E7. 継承チェーン
`MemberController extends Controller`（App）`extends Illuminate\Routing\Controller`。App の `Controller` は `AuthorizesRequests`, `ValidatesRequests` トレイトを use している。
```php
public function __construct() { $this->middleware('auth'); }  // 基底の middleware()
$this->authorize('delete', $member);                          // AuthorizesRequests 由来
$this->validate($request, [...]);                             // ValidatesRequests 由来
```
- 「継承元の継承元（Illuminate 側）／トレイトが提供する機能」を、自前実装せず使うのが要点。E1・E2 と地続き。

## E8. 共通機能の切り出し
メール送信を共通メソッド/サービスへ（[refactoring.md](refactoring.md) の `sendNotification` / `MemberService`）。`store` と `sendMail` の重複を1箇所に。

## E9. レート制限
```php
// ログインにレート制限（routes/web.php）
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1'); // 1分5回
```
- API系なら `RouteServiceProvider` の `RateLimiter::for('api', ...)` を編集。回数超過で 429。

## E10. PHP 容量設定
この教材は `docker/php/Dockerfile` に ini を書いている（`zz-training.ini`）。値を変えて再ビルド：
```ini
upload_max_filesize=20M
post_max_size=22M
memory_limit=256M
```
`docker compose up -d --build` → 反映。`php -i | grep upload_max_filesize` で確認。あわせて Laravel 側の `image|max:20480`（KB）も合わせる。

---

## E11. dd() 調査
**仕込んだバグ**: `resources/views/members/create.blade.php` の ランク `<select name="member_rank">` に対し、`MemberController@store` は `$request->rank` を読んでいる。**名前が不一致**なので rank は常に未指定→`通常(1)` になる。

**dd での特定手順（指導）**:
```php
// store 冒頭
dd($request->all());     // → 'member_rank' はあるが 'rank' が無い、と気づく
// または
dd($request->rank);      // → null（来ていない）
```
**修正**: Blade を `name="rank"` に戻す（または Controller を `$request->member_rank` に合わせる）。**dd はコミット前に削除**。

**指導メモ**: 「値が反映されない系は、まず `dd($request->all())` で“実際に来ているキー/値”を見る」を体で覚えさせる。ログ派には `logger($request->all())` / `\Log::debug(...)` も紹介。
