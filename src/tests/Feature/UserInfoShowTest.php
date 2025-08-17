<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserInfoShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function マイページでプロフィール画像_ユーザーめい_出品した商品一覧_購入した商品一覧が正しく表示される()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'name'   => '山田太郎',
            'avatar' => 'avatars/sample.png',
        ]);

        Item::factory()->create(['user_id' => $user->id, 'name' => '出品A']);
        Item::factory()->create(['user_id' => $user->id, 'name' => '出品B']);

        $seller = User::factory()->create();
        Item::factory()->create(['user_id' => $seller->id, 'name' => '購入X', 'buyer_id' => $user->id]);
        Item::factory()->create(['user_id' => $seller->id, 'name' => '購入Y', 'buyer_id' => $user->id]);

        $this->actingAs($user);

        $respSell = $this->get('/mypage');
        $respSell->assertOk();
        $respSell->assertSee('山田太郎');
        $respSell->assertSee('avatars/sample.png');
        $respSell->assertSee('出品A');
        $respSell->assertSee('出品B');

        $respSell->assertDontSee('購入X');
        $respSell->assertDontSee('購入Y');

        $respPurchase = $this->get('/mypage?tab=purchase');
        $respPurchase->assertOk();
        $respPurchase->assertSee('山田太郎');
        $respPurchase->assertSee('購入X');
        $respPurchase->assertSee('購入Y');
    }
}
