@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/index.css') }}">
@endsection

@section('content')
<div class="mypage-container">
  <div class="mypage-header">
    <div class="avatar-wrapper">
      <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}" class="avatar">
    </div>
    <div class="mypage-info">
      <div class="info-row">
        <div class="user-name">{{ Auth::user()->name }}</div>
        <a href="{{ route('mypage.profile.edit') }}" class="edit-button">プロフィールを編集</a>
      </div>
    </div>
  </div>

  <div class="items__tabs-wrapper">
    <div class="items__tabs">
      <a class="items__tab {{ $tab === 'sell' ? 'items__tab--active' : '' }}" href="{{ url('/mypage?tab=sell') }}">出品した商品</a>
      <a class="items__tab {{ $tab === 'purchase' ? 'items__tab--active' : '' }}" href="{{ url('/mypage?tab=purchase') }}">購入した商品</a>
    </div>
  </div>

  <div class="item__grid">
    @if ($tab === 'sell')
      @forelse($items as $item)
        @include('components.item-card', ['item' => $item])
      @empty
        <p class="items__empty-message">出品した商品はありません。</p>
      @endforelse
    @elseif ($tab === 'purchase')
      @forelse($purchasedItems as $item)
        @include('components.item-card', ['item' =>$item])
      @empty
        <p class="items__empty-message">購入した商品はありません。</p>
      @endforelse
    @endif
  </div>
</div>
@endsection

@section('js')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.tab');
    const lists = document.querySelectorAll('.item-list');

    tabs.forEach(tab => {
      tab.addEventListener('click', function () {
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        lists.forEach(list => list.classList.remove('active'));
        const target = tab.dataset.target;
        document.querySelector(`.item-list.${target}`).classList.add('active');
      });
    });
  });
</script>
@endsection