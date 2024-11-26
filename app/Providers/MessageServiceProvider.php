<?php

namespace App\Providers;

use App\Client\MessageWebhookClient;
use App\Repositories\Contracts\MessageRepositoryInterface;
use App\Repositories\MessageRepository;
use App\Services\CacheService;
use App\Services\Contracts\CacheServiceInterface;
use App\Services\Contracts\MessageServiceInterface;
use App\Services\MessageService;
use Illuminate\Support\ServiceProvider;

class MessageServiceProvider extends ServiceProvider
{
    /**
     * Register all application services.
     */
    public function register(): void
    {
        $this->registerRepository();
        $this->registerClient();
        $this->registerServices();
    }

    /**
     * Register the repository binding.
     */
    private function registerRepository(): void
    {
        $this->app->bind(
            MessageRepositoryInterface::class,
            MessageRepository::class
        );
    }

    /**
     * Register the webhook client.
     */
    private function registerClient(): void
    {
        $this->app->singleton(MessageWebhookClient::class, function () {
            ['url' => $url, 'key' => $key] = config('services.message_webhook');
            return new MessageWebhookClient($url, $key);
        });
    }

    /**
     * Register the services.
     */
    private function registerServices(): void
    {
        $this->app->singleton(CacheServiceInterface::class, CacheService::class);

        $this->app->singleton(MessageServiceInterface::class, function ($app) {
            return new MessageService(
                $app->make(MessageRepositoryInterface::class),
                $app->make(MessageWebhookClient::class),
                $app->make(CacheServiceInterface::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
