@extends('layouts.app')

@section('content')
<h1>会員登録</h1>

<form action="/members" method="post">
    @csrf
    <div class="form-group">
        <label>氏名 <span class="req">必須</span></label>
        <input type="text" name="name" value="{{ old('name') }}">
    </div>
    <div class="form-group">
        <label>カナ</label>
        <input type="text" name="name_kana" value="{{ old('name_kana') }}">
    </div>
    <div class="form-group">
        <label>メールアドレス <span class="req">必須</span></label>
        <input type="text" name="email" value="{{ old('email') }}">
    </div>
    <div class="form-group">
        <label>電話番号</label>
        <input type="text" name="phone" value="{{ old('phone') }}">
    </div>
    <div class="form-group">
        <label>性別 <span class="req">必須</span></label>
        <select name="gender">
            <option value="1">男性</option>
            <option value="2">女性</option>
            <option value="3">その他</option>
        </select>
    </div>
    <div class="form-group">
        <label>生年月日</label>
        <input type="date" name="birthday" value="{{ old('birthday') }}">
    </div>
    <div class="form-group">
        <label>郵便番号</label>
        <input type="text" name="postal_code" value="{{ old('postal_code') }}">
    </div>
    <div class="form-group">
        <label>都道府県</label>
        <input type="text" name="prefecture" value="{{ old('prefecture') }}">
    </div>
    <div class="form-group">
        <label>住所</label>
        <input type="text" name="address" value="{{ old('address') }}">
    </div>
    <div class="form-group">
        <label>ランク</label>
        <select name="member_rank">
            <option value="">通常</option>
            <option value="gold">ゴールド</option>
            <option value="platinum">プラチナ</option>
        </select>
    </div>
    <div class="form-group">
        <label>アバター画像</label>
        <input type="file" name="avatar">
    </div>
    <div class="form-group">
        <label>メモ</label>
        <textarea name="memo">{{ old('memo') }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">登録する</button>
    <a href="/members" class="btn">戻る</a>
</form>
@endsection
