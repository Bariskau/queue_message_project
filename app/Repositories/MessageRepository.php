<?php

namespace App\Repositories;

use App\Enums\MessageStatus;
use App\Models\Message;
use App\Repositories\Contracts\MessageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MessageRepository implements MessageRepositoryInterface
{
    /**
     * Retrieves messages filtered by status.
     *
     * @param MessageStatus $status
     * @return Collection
     */
    public function getMessagesByStatus(MessageStatus $status): Collection
    {
        return Message::query()->where('status', $status->value)
            ->orderBy('id')
            ->get();
    }

    /**
     * Updates message as sent and returns ISO formatted date.
     *
     * @param string $uuid
     * @param string $messageId
     * @return string
     */
    public function markAsSent(string $uuid, string $messageId): string
    {
        $date = now();
        $this->updateMessage($uuid, [
            'status'     => MessageStatus::SENT->value,
            'message_id' => $messageId,
            'sent_at'    => $date
        ]);
        return $date->toISOString();
    }

    /**
     * Updates message by UUID with given payload.
     *
     * @param string $uuid
     * @param array $payload
     * @return bool
     */
    public function updateMessage(string $uuid, array $payload): bool
    {
        $message = Message::query()->where('uuid', $uuid)->first();
        if (!$message) return false;
        return $message->update($payload) > 0;
    }
}
