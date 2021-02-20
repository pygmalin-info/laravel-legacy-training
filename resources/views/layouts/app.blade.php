<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>会員管理システム</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <header class="header">
        <a href="/members" class="logo">会員管理システム</a>
        @auth
        <form action="{{ route('logout') }}" method="post" class="logout-form">
            @csrf
            <span>{{ Auth::user()->name }} さん</span>
            <button type="submit">ログアウト</button>
        </form>
        @endauth
    </header>

    <main class="container">
        @if (session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
