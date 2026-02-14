<?php

declare(strict_types=1);

namespace App\Exception;

final class ForbiddenException extends \Exception
{
    public function __construct(string $message = "Forbidden", int $code = 0, \Throwable|null $previous = null)
    {
        return parent::__construct($message, $code, $previous);
    }
}