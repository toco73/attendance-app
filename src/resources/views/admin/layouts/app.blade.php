<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('/css/reset.css')  }}">
    <link rel="stylesheet" href="{{ asset('/css/common.css')  }}">
    @yield('css')
</head>
<body class="{{ $bodyClass ?? '' }}">
    <header class="header">
        <div class="header__logo">
            <img src="{{asset('img/COACHTECH.png')}}" alt="ロゴ">
        </div>
        @if( !in_array(Route::currentRouteName(),['admin.login']))
        <nav class="header__nav">
            <ul>
                <li>
                    <a href="/admin/attendance/list">勤怠一覧</a>
                </li>
                <li>
                    <a href="{{ route('admin.staff.list') }}">スタッフ一覧</a>
                </li>
                <li>
                    <a href="{{ route('admin.stamp_correction_request.list') }}">申請一覧</a>
                </li>
                <li>
                    <form action="{{ route('admin.logout') }}" method="post">
                        @csrf
                        <button class="header__logout">ログアウト</button>
                    </form>
                </li>
            </ul>
        </nav>
        @endif
    </header>
    <main>
        @yield('content')
        @yield('scripts')
    </main>
</body>
</html>