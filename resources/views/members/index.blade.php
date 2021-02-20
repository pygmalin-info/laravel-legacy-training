@extends('layouts.app')

@section('content')
<div class="page-head">
    <h1>会員一覧</h1>
    <div>
        <a href="/members/create" class="btn btn-primary">新規登録</a>
    </div>
</div>

<table class="list">
    <thead>
        <tr>
            <th>ID</th>
            <th>氏名</th>
            <th>メールアドレス</th>
            <th>都道府県</th>
            <th>同県会員</th>
            <th>ランク</th>
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
