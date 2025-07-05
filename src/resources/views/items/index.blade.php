@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
<div class="items__wrapper">
  @auth
  <div class="items__tabs">
    <a class="items__tab items__tab--active" href="#">おすすめ</a>
    <a class="items__tab" href="#">マイリスト</a>
  </div>
  @endauth

  <div class="items__grid">
    @foreach($items as $item)
    <div class="item-card">
      <div class="item-card__image">
        <img src="{{ asset('' . $item->image_path) }}" alt="商品画像">
        @if($item->is_sold)
        <div class="item-card__sold">Sold</div>
        @endif
      </div>
      <p class="item-card__name">{{ $item->name }}</p>
    </div>
    @endforeach
  </div>
</div>
@endsection