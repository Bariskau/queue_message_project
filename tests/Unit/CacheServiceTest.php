<?php

namespace Tests\Unit;

use App\DTOs\MessageResponseDTO;
use App\Services\CacheService;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Tests\TestCase;

class CacheServiceTest extends TestCase
{
    private CacheService $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        Redis::flushall();
        $this->cacheService = new CacheService();
    }

    public function test_store_message_info()
    {
        // Arrange
        $messageId = Str::uuid()->toString();
        $webhookMessageId = Str::uuid()->toString();
        $dto = new MessageResponseDTO(
            uuid: $messageId,
            messageId: $webhookMessageId,
            sentAt: '2024-01-01T00:00:00Z'
        );

        // Act
        $this->cacheService->storeMessageInfo($dto);

        // Assert
        $cachedData = Redis::hget('messages', $messageId);
        $decodedData = json_decode($cachedData, true);

        $this->assertEquals($webhookMessageId, $decodedData['message_id']);
        $this->assertEquals('2024-01-01T00:00:00Z', $decodedData['sent_at']);
    }
}