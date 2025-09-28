<?php

declare(strict_types=1);

namespace Milenmk\LaravelCrud;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Trait for Error Handling.
 */
trait ErrorHandling
{
    /**
     * Insert error(s) message into the app .log file.
     */
    protected function logError(Exception $e, string $method): void
    {
        Log::error(
            "[$method] Error: {$e->getMessage()}",
            ['class' => $this::class, 'user_id' => auth()->id()]
        );
    }
}
