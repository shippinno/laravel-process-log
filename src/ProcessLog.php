<?php
declare(strict_types=1);

namespace Shippinno\LaravelProcessLog;

use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class ProcessLog
{
    use LoggerAwareTrait;

    /**
     * @var Application
     */
    private Application $app;

    /**
     * @var int
     */
    private int $pid;

    /**
     * @var float|null
     */
    private ?float $startedAt;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);
        $this->pid = getmypid();
        $this->startedAt = microtime(true);
    }

    /**
     * @param Application $app
     */
    public function bootstrap(Application $app): void
    {
        $this->app = $app;
        if (!$app->environment('testing')) {
            register_shutdown_function([$this, 'logShuttingDown']);
            $this->logStartingUp();
        }
    }

    /**
     * @return void
     */
    public function logStartingUp(): void
    {
        $pid = $this->pid;
        if ($this->app->runningInConsole()) {
            $argv = Arr::get($GLOBALS, 'argv');
            $command = sprintf('php %s', implode(' ', (array)$argv));
            $params = compact('command', 'pid');
        } else {
            $method = $_SERVER['REQUEST_METHOD'];
            $uri = $_SERVER['REQUEST_URI'];
            $params = compact('method', 'uri', 'pid');
        }
        $this->logger->info('Starting up process.', $params);
    }

    /**
     * @return void
     */
    public function logShuttingDown(): void
    {
        $pid = $this->pid;
        $time = $this->processingTime();
        $memory = $this->peekMemoryUsage();
        if ($this->app->runningInConsole()) {
            $argv = Arr::get($GLOBALS, 'argv');
            $command = sprintf('php %s', implode(' ', (array)$argv));
            $params = compact('command', 'time', 'memory', 'pid');
        } else {
            $method = $_SERVER['REQUEST_METHOD'];
            $uri = $_SERVER['REQUEST_URI'];
            $params = compact('method', 'uri', 'time', 'memory', 'pid');
        }
        $this->logger->info('Shutting down process.', $params);
    }

    /**
     * @return string
     */
    private function processingTime(): string
    {
        return sprintf('%0.3f[s]', microtime(true) - $this->startedAt);
    }

    /**
     * @param float $time
     * @return string
     */
    protected function formatProcessingTime(float $time): string
    {
        return sprintf('%0.3f[s]', $time);
    }

    /**
     * @return string
     */
    private function peekMemoryUsage(): string
    {
        $usage = memory_get_peak_usage(true);

        return $usage < (1024 * 1024 * 10)
            ? sprintf('%s[kb]', round($usage / 1024, 1))
            : sprintf('%s[mb]', round($usage / (1024 * 1024), 2));
    }
}
