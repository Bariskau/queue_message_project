<?php

namespace App\Services\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface MessageServiceInterface
{
    public function getPendingMessages(): Collection;
    public function getAllSentMessages(): Collection;
    public function sendMessage(string $to, string $content): string;
    public function handleFail(string $uuid): void;
    public function handleMessageResponse(string $uuid, string $webhookMessageId): void;
}
