<?php

namespace App\Services;

use App\Client\MessageWebhookClient;
use App\Enums\MessageStatus;
use App\Events\MessageSent;
use App\Exceptions\MessageSendException;
use App\Repositories\Contracts\MessageRepositoryInterface;
use App\Services\Contracts\MessageServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class MessageService implements MessageServiceInterface
{
    public function __construct(
        private readonly MessageRepositoryInterface $messageRepository,
        private readonly MessageWebhookClient       $client,
    )
    {
    }

    /**
     * Retrieves messages with pending status.
     *
     * @return Collection
     */
    public function getPendingMessages(): Collection
    {
        return $this->messageRepository->getMessagesByStatus(MessageStatus::PENDING);
    }

    /**
     * Retrieves messages with sent status.
     *
     * @return Collection
     */
    public function getAllSentMessages(): Collection
    {
        return $this->messageRepository->getMessagesByStatus(MessageStatus::SENT);
    }

    /**
     * Sends message via webhook client.
     *
     * @param string $to
     * @param string $content
     * @return string
     * @throws MessageSendException
     */
    public function sendMessage(string $to, string $content): string
    {
        try {
            return $this->client->sendMessage($to, $content);
        } catch (\Exception $e) {
            throw new MessageSendException(
                "Failed to send message: {$e->getMessage()}",
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Updates message status to failed.
     *
     * @param string $uuid
     * @return void
     */
    public function handleFail(string $uuid): void
    {
        $this->messageRepository->updateMessage($uuid, [
            'status' => MessageStatus::FAILED->value
        ]);
    }

    /**
     * Updates message status.
     *
     * @param string $uuid
     * @param string $webhookMessageId
     * @return void
     */
    public function handleMessageResponse(string $uuid, string $webhookMessageId): void
    {
        $sent_at = $this->messageRepository->markAsSent($uuid, $webhookMessageId);

        MessageSent::dispatch(
            $uuid,
            $webhookMessageId,
            $sent_at
        );
    }
}
