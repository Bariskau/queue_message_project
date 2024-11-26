<?php

namespace App\Services;

use App\DTOs\MessageResponseDTO;
use App\Services\Contracts\CacheServiceInterface;
use Illuminate\Support\Facades\Redis;

class CacheService implements CacheServiceInterface
{
    /**
     * Stores message data in Redis hash structure.
     *
     * @param MessageResponseDTO $dto Message details container
     */
    public function storeMessageInfo(MessageResponseDTO $dto): void
    {
        Redis::hset('messages', $dto->uuid, json_encode([
            'message_id' => $dto->messageId,
            'sent_at'    => $dto->sentAt
        ]));
    }
}
