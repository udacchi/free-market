<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ItemSearchTest extends TestCase
{
    /** @test */
    public function 商品名の部分一致で検索できる()
    {
        $user = User::factory()->create();
        $items = Item::factory()->count(3)->sequence(
            ['name' => '赤い腕時計',    'condition' => '良好'],
            ['name' => '青い腕時計',    'condition' => '良好'],
            ['name' => 'ゲーミングPC',  'condition' => '良好'],
        )->create();

        DB::table('likes')->insert(
            $items->map(fn ($item) => [
                'user_id'    => $user->id,
                'item_id'    => $item->id,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all()
        );

        /** @var \App\Models\User $user */
        $this->actingAs($user);
        $response = $this->get(route('search', ['keyword' => '腕']));
        $response->assertStatus(200)->assertSee('赤い腕時計')->assertSee('青い腕時計')->assertDontSee('ゲーミングPC');
        $response->assertSee('name="keyword"', false)->assertSee('value="腕"', false);
    }

    /** @test */
    public function 検索状態がマイリストでも保持される()
    {
        $user = User::factory()->create();
        $items = Item::factory()->count(3)->sequence(
            ['name' => '赤い腕時計',    'condition' => '良好'],
            ['name' => '青い腕時計',    'condition' => '良好'],
            ['name' => 'ゲーミングPC',  'condition' => '良好'],
        )->create();

        DB::table('likes')->insert(
            $items->map(fn($item) => [
                'user_id'    => $user->id,
                'item_id'    => $item->id,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all()
        );

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        // 実行：マイリストに keyword を付けてアクセス
        // ルート名が違う場合は 'mylist.index' を置換（例：likes.index など）
        $response = $this->get(route('items.index', ['tab' => 'mylist', 'keyword' => '腕']));

        // 検証：腕が含まれる いいね 済み商品だけ見える
        $response->assertStatus(200)
            ->assertSee('赤い腕時計')
            ->assertSee('青い腕時計')
            ->assertDontSee('ゲーミングPC');

        // 検索キーワードが input に保持されている
        $response->assertSee('name="keyword"', false)
            ->assertSee('value="腕"', false);
    }
}