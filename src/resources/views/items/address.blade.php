@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/address.css') }}">
@endsection

@section('content')
<div class="address-edit">
  <div class="address-edit__inner">
    <h2 class="address-edit__heading content__heading">住所の変更</h2>
    <form action="{{ route('purchase.address.update', ['item' => $item->id]) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="address-edit__group">
        <label class="address-edit__label" for="postal">郵便番号</label>
        <input class="address-edit__input" type="text" name="postal" id="postal" value="{{ old('postal', $user->postal) }}">
      </div>
  
      <div class="address-edit__group">
        <label class="address-edit__label" for="address">住所</label>
        <input class="address-edit__input" type="text" name="address" id="address" value="{{ old('address', $user->address) }}">
      </div>
  
      <div class="address-edit__group">
        <label class="address-edit__label" for="building">建物名</label>
        <input class="address-edit__input" type="text" name="building" id="building" value="{{ old('building', $user->building) }}">
      </div>
  
      <div class="address-edit__button-area">
        <button type="submit" class="address-edit__submit">更新する</button>
      </div>
      
    </form>
  </div>
</div>
@endsection