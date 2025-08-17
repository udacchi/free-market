<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    private function makeItem(User $seller): Item
    {
        return Item::factory()->create([
            'user_id'    => $seller->id,
            'buyer_id'   => null,
            'name'       => 'テスト商品',
            'price'      => 1234,
            'condition'  => '良好',
            'image_path' => 'https://example.com/img.png',
        ]);
    }

    /** @test */
    public function 「購入する」ボタンを押すと購入が完了する()
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create();
        $item   = $this->makeItem($seller);

        /** @var \App\Models\User $buyer */
        $this->actingAs($buyer);

        $addressId = DB::table('addresses')->insertGetId([
            'user_id'   => $buyer->id,
            'name'       => '自宅',
            'postal'    => '1000001',
            'address'   => '東京都千代田区1-1',
            'building'  => 'テストビル',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->post(route('purchase.store', $item), [
            'payment_method' => 'credit',
            'address_id'     => $addressId,
            'postal'         => '1000001',
            'address'        => '東京都千代田区1-1',
            'building'       => 'テストビル',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect();

        $this->assertDatabaseHas('items', [
            'id'       => $item->id,
            'buyer_id' => $buyer->id,
        ]);
    }

    /** @test */
    public function 購入した商品は商品一覧画面で_Sold_表示される()
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create();
        $item   = $this->makeItem($seller);

        /** @var \App\Models\User $buyer */
        $this->actingAs($buyer);

        $addressId = DB::table('addresses')->insertGetId([
            'user_id'   => $buyer->id,
            'name'       => '自宅',
            'postal'    => '1000001',
            'address'   => '東京都千代田区1-1',
            'building'  => 'テストビル',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->post(route('purchase.store', $item), [
            'payment_method' => 'credit',
            'address_id'     => $addressId,
            'postal'  => '1000001',
            'address' => '東京都千代田区1-1',
            'building' => 'テストビル',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $response = $this->get(route('items.index')); 
        $response->assertStatus(200)
            ->assertSee('Sold')
            ->assertSee($item->name);
    }

    /** @test */
    public function 購入した商品がプロフィール購入一覧に表示される()
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create();
        $item   = $this->makeItem($seller);

        /** @var \App\Models\User $buyer */
        $this->actingAs($buyer);

        $addressId = DB::table('addresses')->insertGetId([
            'user_id'   => $buyer->id,
            'name'       => '自宅',
            'postal'    => '1000001',
            'address'   => '東京都千代田区1-1',
            'building'  => 'テストビル',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->post(route('purchase.store', $item), [
            'payment_method' => 'credit',
            'address_id'     => $addressId,
            'postal'  => '1000001',
            'address' => '東京都千代田区1-1',
            'building' => 'テストビル',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $response = $this->get('/mypage?tab=purchase');

        $response->assertStatus(200)
            ->assertSee($item->name);
    }
}
