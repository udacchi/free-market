@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item_show.css') }}">
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
<div class="item-detail__wrapper">
  <div class="item-detail__container">

    <div class="item-detail__image">
      <img src="{{ $item->image_path }}" alt="{{ $item->name }}">
    </div>

    <div class="item-detail__info">
      <h1 class="item-detail__name">{{ $item->name }}</h1>
      <div class="item-detail__brand">{{ $item->brand ?? 'ブランド名' }}</div>
      <div class="item-detail__price">
        ¥{{ number_format($item->price) }} <span class="item-detail__tax">（税込）</span>
      </div>

      <div class="item-detail__icons">
        <form method="POST" action="{{ route('items.like', $item->id) }}" style="text-align: center;">
          @csrf
          @auth
          <button type="submit" class="like-button" style="background: none; border: none; cursor:pointer;">
            @if(auth()->check() && auth()->user()->likedItems->contains($item->id))
               <i class="fas fa-star star-icon"></i>
            @else
               <i class="far fa-star star-icon"></i>
            @endif
          </button>
          @else
          <i class="far fa-star icon"></i>
          @endauth
          <div class="icon-count">{{ $item->likes_count ?? 0 }}</div>
        </form>

        <div class="comment-display" style="text-align: center;">
          <i class="far fa-comment icon"></i>
          <div class="icon-count">{{ $item->comments->count() }}</div>
        </div>
        
      </div>

      <a href="#" class="item-detail__purchase-button">購入手続きへ</a>

      <div class="item-detail__section">
        <h2>商品説明</h2>
        <p><strong>カラー：</strong>{{ $item->color ?? '未指定' }}</p>
        <p>{!! nl2br(e($item->description)) !!}</p>
      </div>

      <div class="item-detail__section">
        <h2>商品の情報</h2>
        <p><strong>カテゴリ：</strong>{{ $item->category->name ?? '未設定' }}</p>
        <p><strong>商品の状態：</strong>{{ $item->condition }}</p>
      </div>

      <div class="item-detail__section">
        <h2>コメント({{ $item->comments->count() }})</h2>
        @foreach($item->comments as $comment)
          <div class="item-comment">
            <div class="item-comment__icon">
              <img src="{{ asset('images/user-icon.png') }}" alt="ユーザーアイコン" class="item-comment__icon-img">
            </div>
            <div class="item-comment__content">
              <div class="item-comment__user">{{ $comment->user->name }}</div>
              <div class="item-comment__text">{{ $comment->body }}</div>
            </div>
          </div>
        @endforeach

        <div class="item-comment-form">
          <form action="{{ route('comments.store', $item->id) }}" method="POST">
            @csrf
            <label for="comment-body" class="item-comment-form__label">商品へのコメント</label>
            <textarea name="body" id="comment-body" class="item-comment-form__textarea" rows="4" required></textarea>
            <button type="submit" class="item-comment-form__submit">コメントを送信する</button>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection