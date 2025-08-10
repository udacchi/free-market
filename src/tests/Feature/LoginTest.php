<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function メールアドレスが未入力の場合_バリデーションが表示される()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function パスワードが未入力の場合_バリデーションが表示される()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function 入力情報が間違っている場合_バリデーションが表示される()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'notfoound@example.com',
            'password' => 'invalidpassword',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('errors');
    }

    /** @test */
    public function 正しい情報が入力された場合_ログイン処理が実行される()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }
}
