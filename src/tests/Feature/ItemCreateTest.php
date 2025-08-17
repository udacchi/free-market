<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 出品フォームから入力した内容がすべて保存される()
    {
        Storage::fake('public');

        // ログインユーザー
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        // カテゴリ（多対多）
        $cat1 = Category::factory()->create(['name' => 'ファッション']);
        $cat2 = Category::factory()->create(['name' => 'メンズ']);

        // 画像（GD不要のフェイク）
        $fakeImage = UploadedFile::fake()->create('item.jpg', 150, 'image/jpeg');

        // 送信データ（実装のname属性に合わせて調整）
        $payload = [
            'name'        => 'テスト商品',
            'description' => 'テスト商品の説明',
            'price'       => 19800,
            'condition'   => '良好',
            // 画像のinput名が 'image' / 'image_path' など、実装に合わせて
            'image'       => $fakeImage,
            // 多対多カテゴリ
            'categories'  => [$cat1->id, $cat2->id],
        ];

        // 出品画面が開ける（任意：画面到達確認）
        $this->get('/sell')->assertOk();

        // 作成実行（RESTful想定）
        $res = $this->post('/items', $payload);

        // 成功後リダイレクト（実装に合わせて調整：詳細へ/トップへ等）
        // 例) items.show へ飛ぶ場合は ->assertRedirect('/items/1') 等に変更
        $res->assertRedirect(); // 具体URLは実装都合があるので汎用に

        // DBに保存されたことを確認
        $this->assertDatabaseHas('items', [
            'name'      => 'テスト商品',
            'price'     => 19800,
            'condition' => '良好',
            'user_id'   => $user->id,
        ]);

        // 作られたItemを取得
        /** @var \App\Models\Item $created */
        $created = Item::firstOrFail();

        // 画像が保存され、パスがモデルに記録されている想定
        // コントローラで store('images', 'public') 等としていれば pass
        $this->assertNotNull($created->image_path ?? null);
        Storage::disk('public')->assertExists($created->image_path);

        // カテゴリのPivotが張られている
        $this->assertDatabaseHas('category_item', [
            'item_id'     => $created->id,
            'category_id' => $cat1->id,
        ]);
        $this->assertDatabaseHas('category_item', [
            'item_id'     => $created->id,
            'category_id' => $cat2->id,
        ]);
    }
}
