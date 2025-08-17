<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingAddressChangeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 配送先住所変更画面にて登録した住所が商品購入画面に反映されている()
    {
        $seller = User::factory()->create(['email_verified_at' => now()]);
        $buyer  = User::factory()->create([
            'email_verified_at' => now(),
            'postal'   => '1000001',
            'address'  => '東京都千代田区1-1',
            'building' => '旧ビル',
        ]);

        $item = Item::factory()->for($seller)->create([
            'buyer_id'  => null,
        ]);

        /** @var \App\Models\User $buyer */
        $this->actingAs($buyer);

        $this->get(route('purchase.show', $item))
            ->assertOk()
            ->assertViewIs('purchase.show')
            ->assertSee('1000001')
            ->assertSee('東京都千代田区1-1')
            ->assertSee('旧ビル')
            ->assertSee(e(route('purchase.address.edit', $item)));

        $this->get(route('purchase.address.edit', $item))
            ->assertOk()
            ->assertViewIs('purchase.address')
            ->assertSee('住所の変更');

        $this->followingRedirects()
            ->put(route('purchase.address.update', $item), [
                'postal'   => '1500001',
                'address'  => '東京都渋谷区1-1',
                'building' => '新ビル',
            ])
            ->assertSee('1500001')
            ->assertSee('東京都渋谷区1-1')
            ->assertSee('新ビル')
            ->assertSee('購入');
    }

    /** @test */
    public function 購入した商品に配送先住所が紐づいて登録される()
    {
        $seller = User::factory()->create(['email_verified_at' => now()]);
        $buyer  = User::factory()->create([
            'email_verified_at' => now(),
            'postal'   => '1500001',
            'address'  => '東京都渋谷区1-1',
            'building' => '新ビル',
        ]);

        $item = Item::factory()->for($seller)->create([
            'buyer_id'  => null,
            'condition' => '良好',
        ]);

        /** @var \App\Models\User $buyer */
        $this->actingAs($buyer);

        $this->post(route('purchase.store', $item), [
            'payment_method' => 'credit',
        ])->assertRedirect(); 

        $this->assertDatabaseHas('items', [
            'id'                => $item->id,
            'buyer_id'          => $buyer->id,
            'payment_method'    => 'credit',
            'shipping_postal'   => '1500001',
            'shipping_address'  => '東京都渋谷区1-1',
            'shipping_building' => '新ビル',
        ]);
    }
}
