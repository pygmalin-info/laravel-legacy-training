@extends('layouts.app')

@section('content')
<div class="login-box">
    <h1>ログイン</h1>
    <form action="/login" method="post">
        @csrf
        <div class="form-group">
            <label>メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}">
        </div>
        <div class="form-group">
            <label>パスワード</label>
            <input type="password" name="password">
        </div>
        <button type="submit" class="btn btn-primary">ログイン</button>
    </form>
    <p class="hint">初期アカウント: admin@example.com / password</p>
</div>
@endsection
