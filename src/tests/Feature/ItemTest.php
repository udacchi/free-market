<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 全商品を取得できる()
    {
        $items = Item::factory()->count(3)->create();

        $response = $this->get('/');

        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    /** @test */
    public function 購入済み商品には_sold_ラベルが表示される()
    {
        $buyer = User::factory()->create();
        $soldItem = Item::factory()->create([
            'buyer_id' => $buyer->id,
        ]);

        $response = $this->get('/?tab=recommend');

        $response->assertSee('Sold');
    }

    /** @test */
    public function 自分が出品した商品は表示されない()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $myItem = Item::factory()->create([
            'user_id' => $user->id,
        ]);

        $otherItem = Item::factory()->create();

        $response = $this->get('/');

        $response->assertSee($otherItem->name);
        $response->assertDontSee($myItem->name);
    }

}
