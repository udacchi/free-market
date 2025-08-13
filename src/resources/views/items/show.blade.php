@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
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
          <form action="{{ $isLiked ? route('likes.destroy', $item) : route('likes.store', $item) }}"
                method="POST" style="display:inline">
            @csrf
            @if($isLiked) @method('DELETE') @endif
            <button type="submit" class="like-button">
              <i class="{{ $isLiked ? 'fa-solid fa-star' : 'fa-regular fa-star' }}"></i>
            </button>
          </form>
          <span class="like-count">{{ $item->likes_count }}</span>
        </div>

        <div class="comment-display" style="text-align: center;">
          <i class="far fa-comment icon"></i>
          <div class="icon-count">{{ $item->comments->count() }}</div>
        </div>
        
      </div>
      
      <a href="{{ route('purchase.show', $item->id) }}" class="item-detail__purchase-button">
        購入手続きへ
      </a>

      <div class="item-detail__section">
        <h2>商品説明</h2>
        <p><strong>カラー：</strong>{{ $item->color ?? '未指定' }}</p>
        <p>{!! nl2br(e($item->description)) !!}</p>
      </div>

      <div class="item-detail__section">
        <h2>商品の情報</h2>
        <p class="item__category"><strong>カテゴリ：</strong>
          @if($item->categories && $item->categories->count())
            <span class="category-tags">
              @foreach($item->categories as $category)
                <span class="category-tag">{{ $category->name }}</span>
              @endforeach
            </span>
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
            <textarea name="body" id="comment-body" class="item-comment-form__textarea" rows="4">{{ old('body') }}</textarea>
            @error('body')
              <div class="item-comment-form__error">{{ $message }}</div>
            @enderror
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