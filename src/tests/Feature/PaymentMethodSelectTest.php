<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodSelectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 選択した支払い方法が正しく反映される()
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create([
            'postal'   => '123-4567',
            'address'  => '東京都港区1-1',
            'building' => 'テストビル',
        ]);

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'price' => 47000,
        ]);

        /** @var \App\Models\User $buyer */
        $this->actingAs($buyer);

        $response = $this->post(route('purchase.store', $item->id), [
            'payment_method' => 'convenience',
        ]);

        $response->assertRedirect(route('items.index'));
        $this->assertDatabaseHas('items', [
            'id'             => $item->id,
            'buyer_id'       => $buyer->id,
            'payment_method' => 'convenience',
        ]);
    }
}
