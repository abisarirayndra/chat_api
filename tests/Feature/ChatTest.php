<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Chat;
use App\Models\User;
use App\Models\ChatLogs;

class ChatTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    public function test_send_chat_to_spesific_user()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $this->actingAs($user);
        $this->post(route('chat.send_to'), [
                        'sender' => $user->id,
                        'phone_number' => $user2->phone_number,
                        'message' => $this->faker->sentence(),
                        'status' => $this->faker->numberBetween(0, 1),
                    ])->assertStatus(201)->assertJsonStructure([
                        'success',
                        'message',
                        'data' => [
                            'sender',
                            'recipient',
                            'message',
                            'status',
                            'updated_at',
                            'created_at',
                            'id'
                        ]
                    ]);

    }
    public function test_show_users_list(){
        $user = User::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $this->actingAs($user);
        $this->get(route('user_list'))->assertStatus(200);

    }
    public function test_reply_in_existing_conversation(){
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $this->actingAs($user);
        $this->post(route('chat.send_to'), [
            'sender' => $user->id,
            'phone_number' => $user2->phone_number,
            'message' => $this->faker->sentence(),
            'status' => $this->faker->numberBetween(0, 1),
        ]);
        $this->actingAs($user2);
        $this->post(route('chat.send_to'), [
            'sender' => $user2->id,
            'phone_number' => $user->phone_number,
            'message' => $this->faker->sentence(),
            'status' => $this->faker->numberBetween(0, 1),
        ])->assertStatus(201);
    }

    public function test_chat_list_with_spesific_user(){
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $this->actingAs($user);
        $this->post(route('chat.send_to'), [
            'sender' => $user->id,
            'phone_number' => $user2->phone_number,
            'message' => $this->faker->sentence(),
            'status' => $this->faker->numberBetween(0, 1),
        ]);
        $this->actingAs($user2);
        $this->get(route('chat.with', [
            'phone_number' => $user->phone_number,
        ]))->assertStatus(200);
    }

    public function test_conversation_history_list(){
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $this->actingAs($user);
        $chat = Chat::factory([
            'sender' => $user->id,
            'recipient' => $user2->id,
            'message' => $this->faker->sentence(),
            'status' => $this->faker->numberBetween(0, 1),
        ])->create();
        $logs = ChatLogs::factory([
            'sender' => $user->id,
            'recipient' => $user2->id,
            'unread_count' => $this->faker->numberBetween(1, 10),
            'latest_message' => $chat->id,
        ])->create();
        $this->get(route('conversation_list'))->assertStatus(200);
    }

    public function test_you_cannot_chat_with_yourself(){
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->get(route('chat.with', [
            'phone_number' => $user->phone_number,
        ]))->assertStatus(404);
    }

    public function test_you_cannot_send_chat_to_yourself(){
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->post(route('chat.send_to'), [
                        'sender' => $user->id,
                        'phone_number' => $user->phone_number,
                        'message' => $this->faker->sentence(),
                        'status' => $this->faker->numberBetween(0, 1),
                    ])->assertStatus(404);
    }

}
