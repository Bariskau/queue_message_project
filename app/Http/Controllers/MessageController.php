<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Services\MessageService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Message API Documentation",
 *     description="API endpoints for managing messages"
 * )
 */
class MessageController extends Controller
{
    public function __construct(private readonly MessageService $messageService)
    {
    }

    /**
     * Get all sent messages
     *
     * @OA\Get(
     *     path="/api/v1/sent-messages",
     *     summary="Retrieve all sent messages",
     *     description="Returns a collection of all sent messages",
     *     operationId="getSentMessages",
     *     tags={"Messages"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="uuid", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
     *                 @OA\Property(property="phone_number", type="string", example="+905551234567"),
     *                 @OA\Property(property="content", type="string", example="Message content"),
     *                 @OA\Property(property="status", type="string", example="sent"),
     *                 @OA\Property(property="message_id", type="string", example="MSG123456"),
     *                 @OA\Property(property="sent_at", type="string", format="datetime", example="2024-01-01 12:00:00")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error"
     *     )
     * )
     */
    public function getSentMessages(): AnonymousResourceCollection
    {
        $messages = $this->messageService->getAllSentMessages();

        return MessageResource::collection($messages);
    }

}
