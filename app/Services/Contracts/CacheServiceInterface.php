<?php

namespace App\Services\Contracts;

use App\DTOs\MessageResponseDTO;

interface CacheServiceInterface
{
    public function storeMessageInfo(MessageResponseDTO $dto): void;
}
