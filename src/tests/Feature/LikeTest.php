<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    public function 作成_ユーザーと商品()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['condition' => '良好']);
        return [$user, $item];
    }

    /** @test */
    public function いいねアイコンを押すといいねした商品として登録され_いいね合計値が増加表示される()
    {
        [$user, $item] = $this->作成_ユーザーと商品();

        /** @var \App\Models\User $user */
        $this->actingAs($user)
             ->followingRedirects()
             ->post(route('likes.store', $item))
             ->assertSee('<span class="like-count">1</span>', false);
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function いいねアイコンを押すと追加済みのアイコンは色が変化して表示される()
    {
        [$user, $item] = $this->作成_ユーザーと商品();

        /** @var \App\Models\User $user */
        $this->actingAs($user)
             ->get(route('items.show', $item))
             ->assertSee('fa-regular fa-star', false);

        $this->actingAs($user)
             ->followingRedirects()
             ->post(route('likes.store',$item))
             ->assertSee('fa-solid fa-star', false);
    }

    /** @test */
    public function 再度いいねアイコンを押すことによりいいねが解除され_いいね合計値が減少表示される()
    {
        [$user, $item] = $this->作成_ユーザーと商品();

        /** @var \App\Models\User $user */
        $this->actingAs($user)
             ->post(route('likes.store', $item));

        $this->actingAs($user)
             ->followingRedirects()
             ->delete(route('likes.destroy', $item))
             ->assertSee('<span class="like-count">0</span>', false)
             ->assertSee('fa-regular fa-star', false);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
