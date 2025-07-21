@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/purchase.css') }}">
@endsection

@section('content')
<div class="purchase__wrapper">
  <div class="purchase-left__container">
    <div class="item-detail__row">
      <div class="item-detail__image">
        <img src="{{ $item->image_path }}" alt="{{ $item->name }}">
      </div>
      <div class="item-detail__info">
        <h2 class="item-detail__name">{{ $item->name }}</h2>
        <p class="item-detail__price">¥{{ number_format($item->price) }}</p>
      </div>
    </div>

    <div class="section-divider"></div>

    <div class="payment-method">
      <label for="payment">支払い方法</label>
      <div class="custom-select-wrapper">
        <select name="payment_method" id="payment" required>
          <option value="">選択してください</option>
          <option value="credit">クレジットカード</option>
          <option value="convenience">コンビニ払い</option>
        </select>
      </div>
    </div>
    

    <div class="section-divider"></div>
  
    <div class="delivery-address">
      <div class="delivery-address__header">
        <label>配送先</label>
        <a href="{{ route('purchase.address.edit', ['item' => $item->id]) }}" class="change-link">変更する</a>
      </div>

      @if(optional($user)->postal || optional($user)->address || optional($user)->building)
        <p>
          〒{{ optional($user)->postal }}<br>
          {{ optional($user)->address }}<br>
          {{ optional($user)->building }}
        </p>
      @else
        <p>配送先情報が未登録です。</p>
      @endif
    </div>

    <div class="section-divider"></div>

  </div>


    
  <div class="purchase-right__container">
    <form action="{{ route('purchase.store', $item->id) }}" method="POST">
      @csrf
      <div class="purchase-summary">
        <table>
          <tr><td>商品代金</td><td>¥{{ number_format($item->price) }}</td></tr>
          <tr><td>支払い方法</td><td id="selected-payment">未選択</td></tr>
        </table>
      </div>
      <button type="submit" class="purchase-button">購入する</button>
    </form>
  </div>
</div>
@endsection

@section('js')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const paymentSelect = document.getElementById('payment');
    const displayPayment = document.getElementById('selected-payment');

    if (paymentSelect && displayPayment) {
      paymentSelect.addEventListener('change', function () {
        const selectedText = paymentSelect.options[paymentSelect.selectedIndex].text;
        displayPayment.textContent = selectedText || '未選択';
      });
    }
  });
</script>