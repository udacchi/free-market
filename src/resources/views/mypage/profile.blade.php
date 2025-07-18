@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/profile.css') }}">
@endsection

@section('search')
<form class="search-form" action="/search" method="get">
  <input class="search-form__keyword-input" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
</form>
@endsection

@section('link')
<div class="header__links">
  <a class="header__link" href="#"
     onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    ログアウト
  </a>
  <form id="logout-form" action="/logout" method="POST" style="display: none;">
    @csrf
  </form>
  <a class="header__link" href="/mypage">マイページ</a>
  <a class="header__submit-button" href="/sell">出品</a>
</div>
@endsection

@section('content')
<div class="profile-edit">
  <h2 class="profile-edit__heading content__heading">プロフィール設定</h2>

  <div class="profile-edit__avatar-area">
    <div class="profile-edit__avatar"></div>
    <button class="profile-edit__image-button">画像を選択する</button>
  </div>

  <form class="profile-edit__form" action="/mypage/profile" method="POST">
    @csrf
    <div class="profile-edit__group">
      <label class="profile-edit__label" for="name">ユーザー名</label>
      <input class="profile-edit__input" type="text" name="name" id="name">
    </div>

    <div class="profile-edit__group">
      <label class="profile-edit__label" for="postal">郵便番号</label>
      <input class="profile-edit__input" type="text" name="postal" id="postal">
    </div>

    <div class="profile-edit__group">
      <label class="profile-edit__label" for="address">住所</label>
      <input class="profile-edit__input" type="text" name="address" id="address">
    </div>

    <div class="profile-edit__group">
      <label class="profile-edit__label" for="building">建物名</label>
      <input class="profile-edit__input" type="text" name="building" id="building">
    </div>

    <div class="profile-edit__button-area">
      <button type="submit" class="profile-edit__submit">更新する</button>
    </div>
  </form>
</div>
@endsection