@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/index.css') }}">
@endsection

@section('search')
<form class="search-form" action="/search" method="get">
  <input class="search-form__keyword-input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
</form>
@endsection

@section('link')
<div class="header__links">
  @auth
    <a class="header__link" href="#"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
      ログアウト
    </a>
    <form id="logout-form" action="/logout" method="POST" style="display: none;">
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
@endsection

@section('content')
<div class="items__tabs-wrapper">
  <div class="items__tabs">
    <a class="items__tab {{ $tab === 'recommend' ? 'items__tab--active' : '' }}" href="{{ url('/') }}">おすすめ</a>
    @auth
      <a class="items__tab {{ $tab === 'mylist' ? 'items__tab--active' : '' }}" href="{{ url('/?tab=mylist') }}">マイリスト</a>
    @else
      <span class="items__tab items__tab--disabled">マイリスト</span>
    @endauth
  </div>

  <div class="items__grid">
    @forelse($items as $item)
      <div class="item-card">
        <a href="{{ route('items.show', $item->id) }}" class="item-card__link">
          <div class="item-card__image">
            <img src="{{ $item->image_path }}" alt="{{ e($item->name) }}">
            @if($item->is_sold)
              <div class="item-card__sold">Sold</div>
            @endif
          </div>
          <p class="item-card__name">{{ $item->name }}</p>
        </a>
      </div>
    @empty
      <p class="items__empty-message">
        @if($tab === 'mylist')
          マイリストに商品がありません。
        @else
          商品が見つかりませんでした。
        @endif
      </p>
    @endforelse
  </div>
    
</div>
@endsection


