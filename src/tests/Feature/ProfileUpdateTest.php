<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function プロフィール編集画面で初期値が表示される()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'name'     => '山田太郎',
            'avatar'   => 'avatars/sample.png',
            'postal'   => '1000001',
            'address'  => '東京都千代田区1-1',
            'building' => 'テストビル101',
        ]);

        $this->actingAs($user);

        $res = $this->get('/mypage/profile');
        $res->assertOk();

        $res->assertSee('avatars/sample.png');
        $res->assertSee('value="山田太郎"', false);
        $res->assertSee('value="1000001"', false);
        $res->assertSee('value="東京都千代田区1-1"', false);
        $res->assertSee('value="テストビル101"', false);
    }

    /** @test */
    public function プロフィール情報を更新でき画像も保存される()
    {
        Storage::fake('public');

        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'name'     => '山田太郎',
            'avatar'   => null,
            'postal'   => '1000001',
            'address'  => '東京都千代田区1-1',
            'building' => 'テストビル101',
        ]);

        $this->actingAs($user);

        $fakeImage = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

        $payload = [
            'name'     => '山田花子',
            'postal'   => '1500001',
            'address'  => '東京都渋谷区1-2-3',
            'building' => '渋谷タワー20F',
            'avatar'   => $fakeImage,
        ];

        $res = $this->put('/mypage/profile', $payload);

        $res->assertRedirect('/');

        $user->refresh();
        $this->assertSame('山田花子', $user->name);
        $this->assertSame('1500001', $user->postal);
        $this->assertSame('東京都渋谷区1-2-3', $user->address);
        $this->assertSame('渋谷タワー20F', $user->building);

        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }
}
