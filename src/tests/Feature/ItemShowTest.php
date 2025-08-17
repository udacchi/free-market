<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;

class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品詳細ページで必要な情報がすべて表示される()
    {
        $seller = User::factory()->create(['name' => '出品太郎']);

        $itemId = DB::table('items')->insertGetId([
            'name'          => '腕時計',
            'brand'         => 'Armani',
            'price'         => 15000,
            'description'   => 'スタイリッシュなデザインのメンズ腕時計',
            'image_path'    => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
            'condition'     => '良好',
            'user_id'       => $seller->id,
            'created_at'    => now(),
            'updated_at'    =>now(),
        ]);

        $item = Item::findOrFail($itemId);

        $catWatchId = DB::table('categories')->insertGetId([
            'name' => '腕時計',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $catFashionId = DB::table('categories')->insertGetId([
            'name' => 'ファッション', 
            'created_at' => now(), 
            'updated_at' => now(),
        ]);


        DB::table('category_item')->insert([
            ['item_id' => $item->id, 'category_id' => $catWatchId],
            ['item_id' => $item->id, 'category_id' => $catFashionId],
        ]);

        $liker1 = User::factory()->create(['name' => '山田太郎']);
        $liker2 = User::factory()->create(['name' => '山田花子']);

        DB::table('likes')->insert([
            ['user_id' => $liker1->id, 'item_id' => $item->id, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $liker2->id, 'item_id' => $item->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('comments')->insert([
            ['user_id' => $liker1->id, 'item_id' => $item->id, 'body' => '購入を検討しています。', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $liker2->id, 'item_id' => $item->id, 'body' => '素敵な時計ですね。', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $response = $this->get(route('items.show', $item));

        $response->assertStatus(200);

        $response->assertSee('腕時計');
        $response->assertSee('Armani');
        $response->assertSee('¥' . number_format($item->price));
        $response->assertSee('スタイリッシュなデザインのメンズ腕時計');
        $response->assertSee('良好');

        $response->assertSee('https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg');

        $response->assertSee('腕時計');
        $response->assertSee('ファッション');

        $response->assertSee('<i class="fa-regular fa-star"></i>', false);
        $response->assertSee('<i class="far fa-comment icon"></i>', false);
        $response->assertSee('コメント(2)');

        $response->assertSeeTextInOrder([
            '購入を検討しています。',
            '素敵な時計ですね。',
        ]);
        $response->assertSee('山田太郎');
        $response->assertSee('山田花子');
    }

    /** @test */
    public function 商品詳細ページで複数選択されたカテゴリ複数選択されたカテゴリが表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create((['user_id' => $user->id]));

        $category1 = Category::create(['name' => 'ファッション']);
        $category2 = Category::create(['name' => 'メンズ']);
        $item->categories()->attach([$category1->id, $category2->id]);

        $response = $this->get(route('items.show', $item->id));

        $response->assertSeeText('ファッション');
        $response->assertSeeText('メンズ');
    }
}
