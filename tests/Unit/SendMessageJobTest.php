<?php

namespace Tests\Unit;

use App\Jobs\Middleware\SendMessageMiddleware;
use Illuminate\Support\Facades\Redis;
use Mockery;
use Tests\TestCase;

class SendMessageJobTest extends TestCase
{
    private SendMessageMiddleware $middleware;
    private $job;

    protected function setUp(): void
    {
        parent::setUp();
        Redis::flushall();

        $this->middleware = new SendMessageMiddleware();
        $this->job = Mockery::mock('Illuminate\Queue\Jobs\Job');
    }

    public function test_rate_limit_allows_only_two_jobs_in_five_seconds()
    {
        // Arrange
        $executedJobs = 0;
        $releasedJobs = 0;

        $this->job->shouldReceive('release')
            ->with(5)
            ->andReturnUsing(function () use (&$releasedJobs) {
                $releasedJobs++;
            });

        $next = function () use (&$executedJobs) {
            $executedJobs++;
        };

        // Act - Try to execute 3 jobs quickly
        $this->middleware->handle($this->job, $next);
        $this->middleware->handle($this->job, $next);
        $this->middleware->handle($this->job, $next);

        // Assert
        $this->assertEquals(2, $executedJobs, 'Should execute exactly 2 jobs');
        $this->assertEquals(1, $releasedJobs, 'Should release 1 job');
    }
}
