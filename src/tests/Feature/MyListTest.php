<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function いいねした商品だけが表示される()
    {
        $user = User::factory()->create();
        $owner1 = User::factory()->create();
        $owner2 = User::factory()->create();

        $likedItemA = Item::factory()->for($owner1, 'user')->create(['name' => 'liked A']);
        $likedItemB = Item::factory()->for($owner2, 'user')->create(['name' => 'liked B']);
        Like::factory()->for($user)->for($likedItemA)->create();
        Like::factory()->for($user)->for($likedItemB)->create();

        $notLiked = Item::factory()->for($owner1, 'user')->create(['name' => 'not liled']);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->get('/?tab=mylist')->assertOk();
        $response->assertSee('liked A')->assertSee('liked B')->assertDontSee('noy liked');
    }

    /** @test */
    public function 購入済み商品には_sold_ラベルが表示される()
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $buyer = User::factory()->create();

        $likedSold = Item::factory()->for($owner, 'user')->create(['name' => 'liked sold', 'buyer_id' => $buyer->id]);
        Like::factory()->for($user)->for($likedSold)->create();

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->get('/?tab=mylist')->assertOk();
        $response->assertSee('liked sold')->assertSee('Sold');
    }

    /** @test */
    public function 自分が出品した商品は表示されない()
    {
        $user = User::factory()->create();
        $myItem = Item::factory()->for($user, 'user')->create(['name' => 'my own item']);
        Like::factory()->for($user)->for($myItem)->create();

        $other = User::factory()->create();
        $othersItem = Item::factory()->for($other, 'user')->create(['name' => 'others item']);
        Like::factory()->for($user)->for($othersItem)->create();

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->get('/?tab=mylist')->assertOk();
        $response->assertSee('others item')->assertDontSee('my own item');
    }

    /** @test */
    public function 未認証の場合は何も表示されない()
    {
        $response = $this->get('/?tab=mylist')->assertOk();
        $response->assertSee('マイリストに商品はありません。');
    }
}