<?php

namespace Tests\Unit;

use App\Enums\MessageStatus;
use App\Models\Message;
use App\Repositories\MessageRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MessageRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private MessageRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new MessageRepository();
    }

    public function test_get_messages_by_status()
    {
        // Arrange
        Message::factory()->count(3)->create(['status' => MessageStatus::PENDING]);
        Message::factory()->count(2)->create(['status' => MessageStatus::SENT]);

        // Act
        $pendingMessages = $this->repository->getMessagesByStatus(MessageStatus::PENDING);
        $sentMessages = $this->repository->getMessagesByStatus(MessageStatus::SENT);

        // Assert
        $this->assertCount(3, $pendingMessages);
        $this->assertCount(2, $sentMessages);
    }

    public function test_mark_as_sent()
    {
        // Arrange
        $message = Message::factory()->create([
            'status' => MessageStatus::PENDING,
        ]);
        $webhookMessageId = Str::uuid()->toString();

        // Act
        $isoDate = $this->repository->markAsSent($message->uuid, $webhookMessageId);
        $message->refresh();

        // Assert
        $this->assertEquals(MessageStatus::SENT, $message->status);
        $this->assertEquals($webhookMessageId, $message->message_id);
        $this->assertNotNull($message->sent_at);
        $this->assertIsString($isoDate);
    }

    public function test_update_message()
    {
        // Arrange
        $message = Message::factory()->create();

        // Act
        $result = $this->repository->updateMessage($message->uuid, [
            'status' => MessageStatus::FAILED->value
        ]);

        // Assert
        $this->assertTrue($result);
        $this->assertEquals(MessageStatus::FAILED, $message->fresh()->status);
    }
}
