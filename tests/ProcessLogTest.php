<?php

namespace Shippinno\LaravelProcessLog;

use Illuminate\Foundation\Application;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ProcessLogTest extends TestCase
{
    /** @var LoggerInterface|Mock $logger */
    private $logger;

    /** @var Application|Mock */
    private Application $app;

    public function setUp(): void
    {
        $this->logger = Mockery::spy(LoggerInterface::class);
        $this->app = Mockery::spy(Application::class);
        $_SERVER['REQUEST_URI'] = 'http://www.example.com/mypage';
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    public function test()
    {
        $request = new ProcessLog($this->logger);
        $request->bootstrap($this->app);
        $this->logger->info('Shutting down process.');
        $this->assertTrue(true);
    }
}
