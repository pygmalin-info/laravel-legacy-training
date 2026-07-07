@extends('layouts.app')

@section('content')
<div class="page-head">
    <h1>会員一覧</h1>
    <div>
        <a href="/members/create" class="btn btn-primary">新規登録</a>
        <a href="/members/csv" class="btn">CSV出力</a>
    </div>
</div>

<form action="/members" method="get" class="search-form">
    <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="氏名・カナ・メール">
    <input type="text" name="phone" value="{{ request('phone') }}" placeholder="電話番号">
    <select name="status">
        <option value="">ステータス</option>
        <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>仮登録</option>
        <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>有効</option>
        <option value="2" {{ request('status')==='2' ? 'selected' : '' }}>退会</option>
    </select>
    <input type="text" name="pref" value="{{ request('pref') }}" placeholder="都道府県">
    <button type="submit" class="btn">検索</button>
</form>

<table class="list">
    <thead>
        <tr>
            <th><a href="/members?sort=id&order=asc">ID</a></th>
            <th><a href="/members?sort=name&order=asc">氏名</a></th>
            <th>メールアドレス</th>
            <th>都道府県</th>
            <th>同県会員</th>
            <th><a href="/members?sort=rank&order=asc">ランク</a></th>
            <th>ステータス</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($members as $m)
        <tr>
            <td>{{ $m->id }}</td>
            <td><a href="/members/{{ $m->id }}">{{ $m->name }}</a></td>
            <td>{{ $m->email }}</td>
            <td>{{ $m->prefecture }}</td>
            <td>{{ $m->same_pref_count }}人</td>
            <td>
                @if ($m->rank == 2) ゴールド
                @elseif ($m->rank == 3) プラチナ
                @else 通常
                @endif
            </td>
            <td>
                @if ($m->status == 0) 仮登録
                @elseif ($m->status == 1) 有効
                @else 退会
                @endif
            </td>
            <td>
                <a href="/members/{{ $m->id }}/edit">編集</a>
                <a href="/members/{{ $m->id }}" onclick="return confirm('削除しますか？')">削除</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $members->links() }}
@endsection
