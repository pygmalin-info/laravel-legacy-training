<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Member;

class MemberController extends Controller
{
    // 一覧・検索
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $status = $request->status;
        $pref = $request->pref;

        $query = Member::query();

        // キーワード検索（名前・カナ・メール）
        if ($keyword != '') {
            $query->where('name', 'like', '%' . $keyword . '%')
                  ->orWhere('name_kana', 'like', '%' . $keyword . '%')
                  ->orWhere('email', 'like', '%' . $keyword . '%');
        }

        // ステータスで絞り込み
        if ($status != '') {
            $query->where('status', $status);
        } else {
            // 退会者は基本出さない
            $query->where('status', '<>', 2);
        }

        // 都道府県で絞り込み
        if ($pref != '') {
            $query->where('prefecture', $pref);
        }

        // 電話番号でも検索できるようにした（あとから追加した機能）
        if ($request->phone != '') {
            $tmp = DB::select("select id from members where phone like '%" . $request->phone . "%'");
            $ids = [];
            foreach ($tmp as $t) {
                $ids[] = $t->id;
            }
            $query->whereIn('id', $ids);
        }

        // 並び替え
        $sort = $request->sort;
        $order = $request->order;
        if ($sort != '') {
            $query->orderBy($sort, $order);
        } else {
            $query->orderBy('id', 'desc');
        }

        $members = $query->paginate(20);

        // 一覧に「同じ都道府県の会員数」を出す
        foreach ($members as $m) {
            $cnt = DB::table('members')
                ->where('prefecture', $m->prefecture)
                ->where('status', 1)
                ->count();
            $m->same_pref_count = $cnt;
        }

        return view('members.index', ['members' => $members]);
    }

    public function show($id)
    {
        $member = Member::find($id);
        if ($member == null) {
            abort(404);
        }

        // 同県の会員数
        $cnt = DB::table('members')->where('prefecture', $member->prefecture)->count();

        return view('members.show', ['member' => $member, 'cnt' => $cnt]);
    }

    public function create()
    {
        return view('members.create');
    }

    // 会員登録
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|email',
            'gender' => 'required',
        ], [
            'name.required' => '氏名を入力してください',
            'email.required' => 'メールアドレスを入力してください',
        ]);

        $member = new Member();
        $member->name = $request->name;
        $member->name_kana = $request->name_kana;
        $member->email = $request->email;
        $member->phone = $request->phone;
        $member->gender = $request->gender;
        $member->birthday = $request->birthday;
        $member->postal_code = $request->postal_code;
        $member->prefecture = $request->prefecture;
        $member->address = $request->address;
        $member->memo = $request->memo;

        // ランク自動判定
        $value = $request->rank;
        if ($value == '') {
            $member->rank = 1;
        } else {
            if ($value == 'gold') {
                $member->rank = 2;
            } else {
                if ($value == 'platinum') {
                    $member->rank = 3;
                } else {
                    $member->rank = 1;
                }
            }
        }

        // 登録直後はまだ仮登録
        $member->status = 0;

        // アバター画像
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $name = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/avatars'), $name);
            $member->avatar = 'uploads/avatars/' . $name;
        }

        $member->save();

        // 登録完了メール
        if ($member->status == 1) {
            $data = [
                'name' => $member->name,
                'body' => 'ご登録ありがとうございます。',
            ];
            Mail::send('emails.notification', $data, function ($mail) use ($member) {
                $mail->to($member->email);
                $mail->from('noreply@example.com', '会員管理システム');
                $mail->bcc('admin@example.com');
                $mail->subject('会員登録が完了しました');
            });
        }

        return redirect('/members')->with('message', '会員を登録しました');
    }

    public function edit($id)
    {
        $member = Member::find($id);
        if ($member == null) {
            abort(404);
        }
        return view('members.edit', ['member' => $member]);
    }

    // 会員更新
    public function update(Request $request, $id)
    {
        $member = Member::find($id);
        if ($member == null) {
            abort(404);
        }

        $member->update($request->all());

        return redirect('/members/' . $id)->with('message', '会員情報を更新しました');
    }

    // 会員削除
    public function destroy($id)
    {
        $member = Member::find($id);
        if ($member != null) {
            $member->delete();
        }
        return redirect('/members')->with('message', '会員を削除しました');
    }

    // CSV出力
    public function csv(Request $request)
    {
        $members = Member::where('status', '<>', 2)->orderBy('id', 'asc')->get();

        $csv = '';
        $csv .= "ID,氏名,カナ,メールアドレス,電話番号,都道府県,ランク,ステータス\n";

        foreach ($members as $m) {
            // ランク表示
            $rank = '';
            if ($m->rank == 1) {
                $rank = '通常';
            }
            if ($m->rank == 2) {
                $rank = 'ゴールド';
            }
            if ($m->rank == 3) {
                $rank = 'プラチナ';
            }

            // ステータス表示
            $st = '';
            if ($m->status == 0) {
                $st = '仮登録';
            }
            if ($m->status == 1) {
                $st = '有効';
            }

            $csv .= $m->id . ',';
            $csv .= $m->name . ',';
            $csv .= $m->name_kana . ',';
            $csv .= $m->email . ',';
            $csv .= $m->phone . ',';
            $csv .= $m->prefecture . ',';
            $csv .= $rank . ',';
            $csv .= $st . "\n";
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="members.csv"',
        ];

        return response($csv, 200, $headers);
    }

    // 個別メール送信
    public function sendMail(Request $request, $id)
    {
        $member = Member::find($id);
        if ($member == null) {
            abort(404);
        }

        $data = [
            'name' => $member->name,
            'body' => $request->body,
        ];
        Mail::send('emails.notification', $data, function ($mail) use ($member) {
            $mail->to($member->email);
            $mail->from('noreply@example.com', '会員管理システム');
            $mail->bcc('admin@example.com');
            $mail->subject('会員管理システムからのお知らせ');
        });

        return redirect('/members/' . $id)->with('message', 'メールを送信しました');
    }

    // 電話番号を整形する（使っていない）
    private function formatPhone($phone)
    {
        $tmp = str_replace('-', '', $phone);
        return substr($tmp, 0, 3) . '-' . substr($tmp, 3, 4) . '-' . substr($tmp, 7);
    }

    // 郵便番号から住所を引く（昔の名残。今は使っていない）
    // private function searchAddress($zip)
    // {
    //     $url = 'http://zipcloud.ibsnet.co.jp/api/search?zipcode=' . $zip;
    //     $json = file_get_contents($url);
    //     $data = json_decode($json, true);
    //     return $data['results'][0]['address1'];
    // }
}
