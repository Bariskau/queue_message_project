<?php

namespace Tests\Feature;

use App\Enums\MessageStatus;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_sent_messages()
    {
        // Arrange
        Message::factory()->count(3)->create([
            'status' => MessageStatus::SENT
        ]);

        // Act
        $response = $this->getJson('/api/v1/sent-messages');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'uuid',
                        'phone_number',
                        'content',
                        'status',
                        'message_id',
                        'sent_at'
                    ]
                ]
            ]);
    }
}
