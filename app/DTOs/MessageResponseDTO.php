<?php

namespace App\DTOs;

class MessageResponseDTO
{
    public function __construct(
        public string $uuid,
        public string $messageId,
        public string $sentAt
    ) {}
}
