<?php

namespace Tests\Unit;

use App\Client\MessageWebhookClient;
use App\Enums\MessageStatus;
use App\Events\MessageSent;
use App\Models\Message;
use App\Repositories\MessageRepository;
use App\Services\MessageService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class MessageServiceTest extends TestCase
{
    private MessageService $service;
    private MessageRepository $repository;
    private MessageWebhookClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(MessageRepository::class);
        $this->client = Mockery::mock(MessageWebhookClient::class);

        $this->service = new MessageService(
            $this->repository,
            $this->client,
        );

        Event::fake([MessageSent::class]);
    }

    public function test_get_pending_messages()
    {
        // Arrange
        $expectedMessages = Message::factory()->count(2)->make();
        $this->repository->shouldReceive('getMessagesByStatus')
            ->with(MessageStatus::PENDING)
            ->once()
            ->andReturn($expectedMessages);

        // Act
        $messages = $this->service->getPendingMessages();

        // Assert
        $this->assertEquals($expectedMessages, $messages);
    }

    public function test_send_message_success()
    {
        // Arrange
        $phone = '+1234567890';
        $content = 'Test message';
        $expectedMessageId = Str::uuid()->toString();

        $this->client->shouldReceive('sendMessage')
            ->with($phone, $content)
            ->once()
            ->andReturn($expectedMessageId);

        // Act
        $messageId = $this->service->sendMessage($phone, $content);

        // Assert
        $this->assertEquals($expectedMessageId, $messageId);
    }

    public function test_handle_message_response_dispatches_event()
    {
        // Arrange
        $uuid = Str::uuid()->toString();
        $webhookMessageId = Str::uuid()->toString();
        $sentAt = '2024-01-01T00:00:00Z';

        $this->repository->shouldReceive('markAsSent')
            ->with($uuid, $webhookMessageId)
            ->once()
            ->andReturn($sentAt);

        // Act
        $this->service->handleMessageResponse($uuid, $webhookMessageId);

        // Assert
        Event::assertDispatched(MessageSent::class, function ($event) use ($uuid, $webhookMessageId, $sentAt) {
            return $event->uuid === $uuid &&
                $event->messageId === $webhookMessageId &&
                $event->sentAt === $sentAt;
        });
    }
}
