@extends('layouts.app')

@section('content')
<h1>会員編集</h1>

<form action="/members/{{ $member->id }}" method="post">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label>氏名</label>
        <input type="text" name="name" value="{{ old('name', $member->name) }}">
    </div>
    <div class="form-group">
        <label>カナ</label>
        <input type="text" name="name_kana" value="{{ old('name_kana', $member->name_kana) }}">
    </div>
    <div class="form-group">
        <label>メールアドレス</label>
        <input type="text" name="email" value="{{ old('email', $member->email) }}">
    </div>
    <div class="form-group">
        <label>電話番号</label>
        <input type="text" name="phone" value="{{ old('phone', $member->phone) }}">
    </div>
    <div class="form-group">
        <label>性別</label>
        <select name="gender">
            <option value="1" {{ $member->gender==1 ? 'selected' : '' }}>男性</option>
            <option value="2" {{ $member->gender==2 ? 'selected' : '' }}>女性</option>
            <option value="3" {{ $member->gender==3 ? 'selected' : '' }}>その他</option>
        </select>
    </div>
    <div class="form-group">
        <label>生年月日</label>
        <input type="date" name="birthday" value="{{ old('birthday', $member->birthday) }}">
    </div>
    <div class="form-group">
        <label>郵便番号</label>
        <input type="text" name="postal_code" value="{{ old('postal_code', $member->postal_code) }}">
    </div>
    <div class="form-group">
        <label>都道府県</label>
        <input type="text" name="prefecture" value="{{ old('prefecture', $member->prefecture) }}">
    </div>
    <div class="form-group">
        <label>住所</label>
        <input type="text" name="address" value="{{ old('address', $member->address) }}">
    </div>
    <div class="form-group">
        <label>ステータス</label>
        <select name="status">
            <option value="0" {{ $member->status==0 ? 'selected' : '' }}>仮登録</option>
            <option value="1" {{ $member->status==1 ? 'selected' : '' }}>有効</option>
            <option value="2" {{ $member->status==2 ? 'selected' : '' }}>退会</option>
        </select>
    </div>
    <div class="form-group">
        <label>メモ</label>
        <textarea name="memo">{{ old('memo', $member->memo) }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">更新する</button>
    <a href="/members/{{ $member->id }}" class="btn">戻る</a>
</form>
@endsection
