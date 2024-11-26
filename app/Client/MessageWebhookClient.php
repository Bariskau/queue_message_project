<?php

namespace App\Client;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class MessageWebhookClient
{
    private PendingRequest $client;

    public function __construct(string $url, string $key)
    {
        $this->client = Http::baseUrl($url)->withHeaders([
            'Content-Type'   => 'application/json',
            'x-ins-auth-key' => $key
        ]);
    }


    /**
     * Send a message to a specified phone number.
     *
     * @param string $phoneNumber
     * @param string $content
     * @return string
     * @throws \Exception
     */
    public function sendMessage(string $phoneNumber, string $content): string
    {
        $response = $this->client->post('', [
            'to'      => $phoneNumber,
            'content' => $content
        ]);

        if ($response->status() === 202) {
            return $response->json('messageId');
        }

        throw new \Exception('Failed to send message: ' . $response->body());
    }
}
