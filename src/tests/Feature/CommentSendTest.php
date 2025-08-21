<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CommentSendTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン済みユーザーはコメントを送信できる()
    {
        $seller = User::factory()->create();
        $item   = Item::factory()->for($seller)->create();
        $user   = User::factory()->create();

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $url = Route::has('comments.store') ? route('comments.store', $item) : "/item/{$item->id}/comments";

        $response = $this->post($url, [
            'body' => 'テストコメント',
            'body' => 'テストコメント',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function ログイン前のユーザーはコメントを送信できない()
    {
        $seller = User::factory()->create();
        $item   = Item::factory()->for($seller)->create();

        $url = Route::has('comments.store') ? route('comments.store', $item) : "/item/{$item->id}/comments";

        $response = $this->post($url, [
            'body' => 'ゲスト投稿',
            'body' => 'ゲスト投稿',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('comments', 0);
        $this->assertGuest();
    }

    /** @test */
    public function コメントが入力されていない場合_バリデーションメッセージが表示される()
    {
        $seller = User::factory()->create();
        $item   = Item::factory()->for($seller)->create();
        $user   = User::factory()->create();
        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $show = Route::has('items.show') ? route('items.show', $item) : "/item/{$item->id}";
        $url  = Route::has('comments.store') ? route('comments.store', $item) : "/item/{$item->id}/comments";

        $response = $this->from($show)->post($url, [
            'body' => '',
            'body' => '',
        ]);

        $response->assertRedirect($show);
        $response->assertSessionHasErrors(); 
        $this->assertDatabaseCount('comments', 0);
    }

    /** @test */
    public function コメントが255字以上の場合_バリデーションメッセージが表示される()
    {
        $seller = User::factory()->create();
        $item   = Item::factory()->for($seller)->create();
        $user   = User::factory()->create();
        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $tooLong = str_repeat('あ', 256);
        $show = Route::has('items.show') ? route('items.show', $item) : "/item/{$item->id}";
        $url  = Route::has('comments.store') ? route('comments.store', $item) : "/item/{$item->id}/comments";

        $response = $this->from($show)->post($url, [
            'content' => $tooLong,
            'comment' => $tooLong,
        ]);

        $response->assertRedirect($show);
        $response->assertSessionHasErrors(); 
        $this->assertDatabaseCount('comments', 0);
    }
}
