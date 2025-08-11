<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>FREE-MARKET</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  @yield('css')
</head>

<body>
  <div class="app">
    <header class="header">
      <a href="/" class="header__logo">
        <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH" width="370" height="36">
      </a>
      <!-- 検索フォーム -->
      @if (View::hasSection('search'))
      @yield('search')
      @else
      <div class="header__search">
        <form class="search-form" action="/search" method="get">
          <input class="search-form__keyword-input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword', $keyword ?? '') }}">
        </form>
      </div>
      @endif

<!-- ヘッダーリンク -->
      @if (View::hasSection('link'))
      @yield('link')
      @else
        <div class="header__links">
          @auth
          <a class="header__link" href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              ログアウト
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
          <a class="header__link" href="/mypage">マイページ</a>
          <a class="header__submit-button" href="/sell">出品</a>
          @else
          <a class="header__link" href="{{ route('login') }}">ログイン</a>
          <a class="header__link" href="{{ route('mypage') }}">マイページ</a>
          <a class="header__submit-button" href="/sell">出品</a>
          @endauth
        </div>
      @endif
    </header>
    <div class="content">
      @yield('content')
    </div>
  </div>
  @yield('js')
</body>

</html>