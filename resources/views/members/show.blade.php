@extends('layouts.app')

@section('content')
<div class="page-head">
    <h1>{{ $member->name }} さんの情報</h1>
    <div>
        <a href="/members/{{ $member->id }}/edit" class="btn">編集</a>
        <a href="/members/{{ $member->id }}" class="btn btn-danger" onclick="return confirm('削除しますか？')">削除</a>
    </div>
</div>

<table class="detail">
    <tr><th>ID</th><td>{{ $member->id }}</td></tr>
    <tr><th>氏名</th><td>{{ $member->name }}</td></tr>
    <tr><th>カナ</th><td>{{ $member->name_kana }}</td></tr>
    <tr><th>メールアドレス</th><td>{{ $member->email }}</td></tr>
    <tr><th>電話番号</th><td>{{ $member->phone }}</td></tr>
    <tr>
        <th>性別</th>
        <td>
            @if ($member->gender == 1) 男性
            @elseif ($member->gender == 2) 女性
            @else その他
            @endif
        </td>
    </tr>
    <tr><th>生年月日</th><td>{{ date('Y年n月j日', $member->birthday) }}</td></tr>
    <tr><th>住所</th><td>{{ $member->prefecture }}{{ $member->address }}</td></tr>
    <tr><th>同じ都道府県の会員</th><td>{{ $cnt }}人</td></tr>
    <tr>
        <th>ランク</th>
        <td>
            @if ($member->rank == 2) ゴールド
            @elseif ($member->rank == 3) プラチナ
            @else 通常
            @endif
        </td>
    </tr>
    <tr>
        <th>ステータス</th>
        <td>
            @if ($member->status == 0) 仮登録
            @elseif ($member->status == 1) 有効
            @else 退会
            @endif
        </td>
    </tr>
    <tr><th>メモ</th><td>{!! $member->memo !!}</td></tr>
</table>

<p><a href="/members">← 一覧へ戻る</a></p>
@endsection
