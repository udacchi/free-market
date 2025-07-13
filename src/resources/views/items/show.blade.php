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
        <div class="like-button-wrapper">
          <button id="like-button" data-item-id="{{ $item->id }}" class="like-button">
            @if(auth()->check() && auth()->user()->likedItems->contains($item->id))
              <i class="fas fa-star star-icon liked"></i>
            @else
              <i class="far fa-star star-icon not-liked"></i>
            @endif
          </button>
          <div class="icon-count">{{ $item->liked_by_users_count ?? 0 }}</div>
        </div>

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
        <p class="item__category"><strong>カテゴリ：</strong>
          @if($item->categories && $item->categories->count())
            @foreach($item->categories as $category)
              <span class="category__name">{{ $category->name }}</span>{{ !$loop->last ? ' / ' : '' }}
            @endforeach
          @else
              未設定
          @endif
        </p>

        <p class="item__condition"><strong>商品の状態：</strong>
          <span class="condition__value">{{ $item->condition }}</span>
        </p>
      </div>

      <div class="item-detail__section">
        <h2>コメント({{ $item->comments->count() }})</h2>
        @foreach($item->comments as $comment)
        <div class="item-comment">
          <div class="item-comment__header">
            <img src="{{ asset('storage/' . ($comment->user->profile_image ?? 'images/user-icon.png')) }}" class="item-comment__icon-img" >
            <div class="item-comment__meta">
              <div class="item-comment__user">{{ $comment->user->name }}</div>
            </div>
          </div>
          <div class="item-comment__text">{{ $comment->body }}</div>
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

@section('js')
<script>
  document.getElementById('like-button')?.addEventListener('click', function (e) {
    e.preventDefault();
    const itemId = this.dataset.itemId;

    fetch(`/items/${itemId}/like`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      }
    }).then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          // アイコンの切り替え
          const icon = this.querySelector('i');
          const countEl = document.querySelector('.icon-count');
          let count = parseInt(countEl.textContent);

          if (icon.classList.contains('fas')) {
            // すでにいいね済 → 解除
            icon.classList.remove('fas', 'liked');
            icon.classList.add('far', 'not-liked');
            countEl.textContent = count - 1;
          } else {
            // 未いいね → 登録
            icon.classList.remove('far', 'not-liked');
            icon.classList.add('fas', 'liked');
            countEl.textContent = count + 1;
          }
        }
      });
  });
</script>
@endsection