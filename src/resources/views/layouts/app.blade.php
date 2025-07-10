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
      @yield('search')
      @yield('link')
      @yield('submit')
    </header>
    <div class="content">
      @yield('content')
    </div>
  </div>
</body>

</html>