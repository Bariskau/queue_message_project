<?php

namespace App\Console\Commands;

use App\Jobs\SendMessage;
use App\Services\MessageService;
use Illuminate\Console\Command;

class ProcessMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending messages';

    /**
     * Execute the console command.
     */
    public function handle(MessageService $messageService)
    {
        $messages = $messageService->getPendingMessages();

        foreach ($messages as $message) {
            SendMessage::dispatch($message);
        }
    }
}
