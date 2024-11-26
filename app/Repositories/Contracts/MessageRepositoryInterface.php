<?php

namespace App\Repositories\Contracts;

use App\Enums\MessageStatus;
use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;

interface MessageRepositoryInterface
{
    public function getMessagesByStatus(MessageStatus $status): Collection;

    public function markAsSent(string $uuid, string $messageId): string;

    public function updateMessage(string $uuid, array $payload): bool;

}
