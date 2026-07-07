# リファクタリング模範解答

バグ修正の次のステップ。**「動くけど汚い」コードを整える**。
受講生は Service / Repository をこれから学ぶレベルなので、**まずは FormRequest・定数化・重複排除**から入り、
最後に Service 切り出しを「発展課題」として扱うのがおすすめ。

---

## 1. マジックナンバーを定数にする

`status` `rank` `gender` が数字のまま各所に散っている。まずは定数クラスに集約する。

```php
// app/Consts/MemberConst.php
namespace App\Consts;

class MemberConst
{
    // status
    const STATUS_PENDING = 0; // 仮登録
    const STATUS_ACTIVE  = 1; // 有効
    const STATUS_LEFT    = 2; // 退会

    const STATUS_LABELS = [
        self::STATUS_PENDING => '仮登録',
        self::STATUS_ACTIVE  => '有効',
        self::STATUS_LEFT    => '退会',
    ];

    // rank
    const RANK_NORMAL   = 1;
    const RANK_GOLD     = 2;
    const RANK_PLATINUM = 3;

    const RANK_LABELS = [
        self::RANK_NORMAL   => '通常',
        self::RANK_GOLD     => 'ゴールド',
        self::RANK_PLATINUM => 'プラチナ',
    ];
}
```

Blade もラベル配列で書ける:
```blade
{{ \App\Consts\MemberConst::STATUS_LABELS[$member->status] ?? '不明' }}
```

> PHP 8.1+ なら `enum` にするとより堅い。この教材は 8.0 想定なので定数クラスで十分。

---

## 2. バリデーションを FormRequest に切り出す

登録・編集で同じルールを使い回す。Controller が痩せる。

```php
// app/Http/Requests/MemberRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // 編集時は自分自身を unique から除外
        $id = $this->route('id');

        return [
            'name'      => 'required|max:50',
            'name_kana' => 'nullable|max:100',
            'email'     => 'required|email|unique:members,email,'.$id,
            'phone'     => 'nullable|max:20',
            'gender'    => 'required|in:1,2,3',
            'birthday'  => 'nullable|date',
            'avatar'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'name.required'  => '氏名を入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.unique'   => 'このメールアドレスは既に登録されています',
        ];
    }
}
```

Controller 側:
```php
public function store(MemberRequest $request) { ... }
public function update(MemberRequest $request, $id) { ... }
```

---

## 3. メール送信の重複をまとめる（＋ハードコード排除）

`store` と `sendMail` に同じ送信処理がある。設定は config に出す。

```php
// config/mail.php に追記、または config/services.php
'member_notice' => [
    'from'    => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
    'from_name' => env('MAIL_FROM_NAME', '会員管理システム'),
    'admin_bcc' => env('MAIL_ADMIN_BCC', 'admin@example.com'),
],
```

```php
// 共通メソッド（Service へ移すのが理想）
private function sendNotification(Member $member, string $subject, string $body)
{
    $cfg = config('mail.member_notice');
    Mail::send('emails.notification', ['name' => $member->name, 'body' => $body],
        function ($mail) use ($member, $subject, $cfg) {
            $mail->to($member->email)
                 ->from($cfg['from'], $cfg['from_name'])
                 ->bcc($cfg['admin_bcc'])
                 ->subject($subject);
        });
}
```

---

## 4. ネストを浅くする（ランク判定）

```php
// Before: if の三重ネスト
// After: マップで引く
private const RANK_MAP = ['gold' => 2, 'platinum' => 3];

$member->rank = self::RANK_MAP[$request->rank] ?? MemberConst::RANK_NORMAL;
```

---

## 5. CSV を fputcsv + BOM で堅くする

```php
public function csv()
{
    $members = Member::where('status', '<>', MemberConst::STATUS_LEFT)->orderBy('id')->get();

    $callback = function () use ($members) {
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // Excel 用 BOM
        fputcsv($out, ['ID', '氏名', 'カナ', 'メールアドレス', '電話番号', '都道府県', 'ランク', 'ステータス']);
        foreach ($members as $m) {
            fputcsv($out, [
                $m->id, $m->name, $m->name_kana, $m->email, $m->phone, $m->prefecture,
                MemberConst::RANK_LABELS[$m->rank] ?? '',
                MemberConst::STATUS_LABELS[$m->status] ?? '',
            ]);
        }
        fclose($out);
    };

    return response()->streamDownload($callback, 'members.csv', ['Content-Type' => 'text/csv']);
}
```

---

## 6. 検索ロジックを Service へ（発展）

Fat Controller の核。検索条件組み立てを `MemberService` に逃がす。

```php
// app/Services/MemberService.php
namespace App\Services;

use App\Models\Member;

class MemberService
{
    public function search(array $cond)
    {
        $query = Member::query();

        if (!empty($cond['keyword'])) {
            $query->where(function ($q) use ($cond) {
                $q->where('name', 'like', '%'.$cond['keyword'].'%')
                  ->orWhere('name_kana', 'like', '%'.$cond['keyword'].'%')
                  ->orWhere('email', 'like', '%'.$cond['keyword'].'%');
            });
        }
        if (isset($cond['status']) && $cond['status'] !== '') {
            $query->where('status', $cond['status']);
        } else {
            $query->where('status', '<>', \App\Consts\MemberConst::STATUS_LEFT);
        }
        if (!empty($cond['pref'])) {
            $query->where('prefecture', $cond['pref']);
        }
        if (!empty($cond['phone'])) {
            $query->where('phone', 'like', '%'.$cond['phone'].'%');
        }
        return $query;
    }
}
```

Controller は薄くなる:
```php
public function index(Request $request, MemberService $service)
{
    $members = $service->search($request->all())
        ->orderBy(...)      // whitelist 済み
        ->paginate(20);
    // N+1 対策の集計を付与
    return view('members.index', compact('members'));
}
```

---

## 到達目標

- Controller が **300行 → 100行程度**まで痩せる
- マジックナンバー・ハードコードがコードから消える
- 登録・編集で同じ入力チェックが効く
- 一覧の SQL 数が件数に依存しない
- 不要コード（`formatPhone`, コメントアウト, TODO）が消える
