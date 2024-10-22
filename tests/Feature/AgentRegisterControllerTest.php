<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AgentRegisterControllerTest extends TestCase
{
    use RefreshDatabase; // テスト実行時にデータベースをリセット

    /** @test */
    public function it_displays_the_agent_registration_form()
    {
        // 代理店の登録フォームが表示されるかテスト
        $response = $this->get(route('agent.register'));

        // 正常なステータスコード(200)で表示されることを確認
        $response->assertStatus(200);
        $response->assertViewIs('auth.agent-register'); // ビューが正しいか確認
    }

    /** @test */
    public function it_registers_a_new_agent_user()
    {
        // フォーム送信のデータ
        $formData = [
            'name' => 'Test Agent',
            'email' => 'agent@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        // 代理店ユーザーの登録処理をテスト
        $response = $this->post(route('agent.register'), $formData);

        // ユーザーがデータベースに保存されているか確認
        $this->assertDatabaseHas('users', [
            'email' => 'agent@example.com',
            'role' => 'agent', // ロールが代理店になっているか確認
        ]);

        // ログインしているか確認
        $this->assertAuthenticated();

        // ダッシュボードへのリダイレクトを確認
        $response->assertRedirect(route('dashboard'));
    }
}
