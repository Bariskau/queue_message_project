<?php

namespace App\Jobs\Middleware;

use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\Redis;

class SendMessageMiddleware
{
    /**
     * @param Job $job
     * @param $next
     * @return void
     * @throws \Illuminate\Contracts\Redis\LimiterTimeoutException
     */
    public function handle($job, $next): void
    {
        Redis::throttle('send_message')
            ->block(0)
            ->allow(2)
            ->every(5)
            ->then(function () use ($job, $next) {
                $next($job);
            }, function () use ($job) {
                $job->release(5);
            });
    }
}
