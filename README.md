# Laravel Process Log

## Installation

```sh
composer require shippinno/laravel-process-log
```

## Setup

### For Artisan commands logging

Let `Shippinno\ProcessLog\ProcessLog::class` to be included in bootstrappers of your Console Kernel (usually `App\Console\Kernel`).

```php
class Kernel extends ConsoleKernel
{
    // ...
 
    protected function bootstrappers()
    {
        return array_merge(
            $this->bootstrappers,
            [ProcessLog::class]
        );
    }
}
```

Run an Artisan command and it will be logged like below.

```
[2019-01-22 15:20:13] production.INFO: Starting up process. {"command":"php artisan some:command","pid":12345} []
[2019-01-22 15:20:21] production.INFO: Shutting down process. {"time":"8.258[s]","memory":"34[mb]","pid":12345} []
```

### For HTTP requests logging

Same for your HTTP Kernel (usually `App\Http\Kernel`).

```php
class Kernel extends HttpKernel 
{
    // ...
 
    protected function bootstrappers()
    {
        return array_merge(
            $this->bootstrappers,
            [ProcessLog::class]
        );
    }
}
```

Make an HTTP request and it will be logged like below.

```
[2019-01-22 15:41:26] production.INFO: Starting up process. {"method":"GET","uri":"/foo/bar?baz","pid":12345} []
[2019-01-22 15:41:26] production.INFO: Shutting down process. {"time":"0.386[s]","memory":"2048[kb]","pid":12345} []
```

Logging is disabled if the app environment is set to `testing`.

