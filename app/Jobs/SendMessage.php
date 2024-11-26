<?php

namespace App\Jobs;

use App\Jobs\Middleware\SendMessageMiddleware;
use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessage implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Only fail the job if there is an error that is not handled
     * @var int
     */
    public int $maxExceptions = 3;


    /**
     * Create a new job instance.
     */
    public function __construct(private Message $message)
    {
    }

    /**
     * @return mixed
     */
    public function uniqueId()
    {
        return $this->message->uuid;
    }

    /**
     * Add a rate limit middleware for the job.
     * TODO It is a much more logical solution to create and schedule different windows before dispatching jobs.
     * @return SendMessageMiddleware[]
     */
    public function middleware(): array
    {
        return [
            new SendMessageMiddleware()
        ];
    }

    /**
     * When using rate limiting, the number of attempts of your job may be hard to predict.
     * @return \DateTimeInterface
     */
    public function retryUntil(): \DateTimeInterface
    {
        return now()->addDay();
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(MessageService $messageService): bool
    {
        $messageId = $messageService->sendMessage($this->message->phone_number, $this->message->content);
        $messageService->handleMessageResponse($this->message->uuid, $messageId);

        return true;
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $e): void
    {
        $messageService = app(MessageService::class);
        $messageService->handleFail($this->message->uuid);
    }
}
