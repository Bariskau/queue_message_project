<?php

namespace App\Listeners;

use App\DTOs\MessageResponseDTO;
use App\Events\MessageSent;
use App\Services\Contracts\CacheServiceInterface;

class CacheMessageInfo
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly CacheServiceInterface $cacheService
    )
    {
    }


    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        $this->cacheService->storeMessageInfo(
            new MessageResponseDTO(
                uuid: $event->uuid,
                messageId: $event->messageId,
                sentAt: $event->sentAt
            )
        );
    }
}
