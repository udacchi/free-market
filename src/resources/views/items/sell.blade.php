@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/sell.css') }}">
@endsection

@section('content')
<div class="sell-form__container">
  <h2 class="sell-form__heading content__heading">商品の出品</h2>

  {{-- エラー表示 --}}
  @if ($errors->any())
    <div class="sell-form__errors">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" novalidate>
    @csrf

    <div class="sell-form__section">
      <label class="sell-form__label">商品画像</label>
      <div class="image-upload-box">
        <label class="image-upload-label" for="image">画像を選択する</label>
        <input type="file" id="image" name="image" accept="image/*" class="image-input">
      </div>
      @error('image')
        <p id="err-image" class="error-text">{{ $message }}</p>
      @enderror
    </div>

    <div class="sell-form__section">
      <h3 class="sell-form__section-title">商品の詳細</h3>

      <label class="sell-form__label">カテゴリー</label>
      <div class="category-tags">
        @foreach($categories as $category)
          <input type="checkbox" name="categories[]" id="category_{{ $category->id }}" value="{{ $category->id }}" hidden
            {{ in_array($category->id, old('categories', $selected ?? [])) ? 'checked' : '' }}>
          <label for="category_{{ $category->id }}" class="category-tag">
            {{ $category->name }}
          </label>
        @endforeach
      </div>
      @error('categories')
        <p class="error-text">{{ $message }}</p>
      @enderror
      @foreach ($errors->get('categories.*') as $messages)
        @foreach ($messages as $message)
          <p class="error-text">{{ $message }}</p>
        @endforeach
      @endforeach

      <label class="sell-form__label">商品の状態</label>
      <div class="custom-select-wrapper">
        <select name="condition" required>
          <option value="">選択してください</option>
          <option value="良好" {{ old('condition') == '良好' ? 'selected' : '' }}>良好</option>
          <option value="目立った傷や汚れなし" {{ old('condition') == '目立った傷や汚れなし' ? 'selected' : '' }}>目立った傷や汚れなし</option>
          <option value="やや傷や汚れあり" {{ old('condition') == 'やや傷や汚れあり' ? 'selected' : '' }}>やや傷や汚れあり</option>
          <option value="状態が悪い" {{ old('condition') == '状態が悪い' ? 'selected' : '' }}>状態が悪い</option>
        </select>
      </div>
      @error('condition')
        <p id="err-condition" class="error-text">{{ $message }}</p>
      @enderror
    </div>

    <div class="sell-form__section">
      <h3 class="sell-form__section-title">商品名と説明</h3>

      <div class="sell-form__group">
        <label class="sell-form__label" for="name">商品名</label>
        <input class="sell-form__input" type="text" name="name" id="name" value="{{ old('name') }}" required>
        @error('name')
          <p id="err-name" class="error-text">{{ $message }}</p>
        @enderror
      </div>

      <div class="sell-form__group">
        <label class="sell-form__label" for="brand">ブランド名</label>
        <input class="sell-form__input" type="text" name="brand" id="brand" value="{{ old('brand') }}">
        @error('brand')
          <p id="err-brand" class="error-text">{{ $message }}</p>
        @enderror
      </div>

      <div class="sell-form__group">
        <label class="sell-form__label" for="description">商品の説明</label>
        <textarea class="sell-form__textarea" name="description" id="description" rows="4" required>{{ old('description') }}</textarea>
        @error('description')
          <p id="err-description" class="error-text">{{ $message }}</p>
        @enderror
      </div>

      <div class="sell-form__group">
        <label class="sell-form__label" for="price">販売価格</label>
        <div class="sell-price__input-wrapper">
          <span class="sell-price__prefix">¥</span>
          <input class="sell-form__input sell-price__input" type="number" name="price" id="price" value="{{ old('price') }}" required>
        </div>
        @error('price')
          <p id="err-price" class="error-text">{{ $message }}</p>
        @enderror
      </div>
    </div>

    <div class="sell-form__submit">
      <button type="submit" class="sell-form__submit-button">出品する</button>
    </div>
  </form>
</div>
@endsection
