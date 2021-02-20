<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Member;

class MemberController extends Controller
{
    // 一覧
    public function index(Request $request)
    {
        $members = Member::where('status', '<>', 2)->orderBy('id', 'desc')->paginate(20);

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

        $member->save();

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

    // 電話番号を整形する（使っていない）
    private function formatPhone($phone)
    {
        $tmp = str_replace('-', '', $phone);
        return substr($tmp, 0, 3) . '-' . substr($tmp, 3, 4) . '-' . substr($tmp, 7);
    }
}
