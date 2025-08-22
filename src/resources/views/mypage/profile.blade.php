@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/profile.css') }}">
@endsection

@section('content')
<div class="profile-edit">
  <h2 class="profile-edit__heading content__heading">プロフィール設定</h2>

  <form class="profile-edit__form" action="{{ url('/mypage/profile') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="profile-edit__avatar-area">
      <div class="profile-edit__avatar">
        @if ($user->avatar)
          <img src="{{ asset('storage/' . $user->avatar) }}" alt="プロフィール画像">
        @else
          <div class="profile-edit__avatar--placeholder">
            {{ $user->name }}
          </div>
        @endif
      </div>

      <label for="avatar" class="profile-edit__image-button">画像を選択する</label>
      <input type="file" id="avatar" name="avatar" accept=".jpeg,.jpg,.png" style="display: none;">
      <p class="profile-edit__error-message">
        @error('avatar') {{ $message }} @enderror
      </p>
    </div>

    <div class="profile-edit__group">
      <label class="profile-edit__label" for="name">ユーザー名</label>
      <input class="profile-edit__input" type="text" name="name" id="name" value="{{ old('name', $user->name) }}">
      @error('name')
        <p class="profile-edit__error-message">{{ $message }}</p>
      @enderror
    </div>

    <div class="profile-edit__group">
      <label class="profile-edit__label" for="postal">郵便番号</label>
      <input class="profile-edit__input" type="text" name="postal" id="postal" value="{{ old('postal', $user->postal) }}">
      @error('postal')
        <p class="profile-edit__error-message">{{ $message }}</p>
      @enderror
    </div>

    <div class="profile-edit__group">
      <label class="profile-edit__label" for="address">住所</label>
      <input class="profile-edit__input" type="text" name="address" id="address" value="{{ old('address', $user->address) }}">
      @error('address')
        <p class="profile-edit__error-message">{{ $message }}</p>
      @enderror
    </div>

    <div class="profile-edit__group">
      <label class="profile-edit__label" for="building">建物名</label>
      <input class="profile-edit__input" type="text" name="building" id="building" value="{{ old('building', $user->building) }}">
      @error('building')
        <p class="profile-edit__error-message">{{ $message }}</p>
      @enderror
    </div>

    <div class="profile-edit__button-area">
      <button type="submit" class="profile-edit__submit">更新する</button>
    </div>
  </form>
</div>
@endsection
