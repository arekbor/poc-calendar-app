<?php

declare(strict_types=1);

namespace App\Exception;

final class UnauthorizedException extends \Exception 
{
    public function __construct(string $message = "Unauthorized", int $code = 0, \Throwable|null $previous = null)
    {
        return parent::__construct($message, $code, $previous);
    }
}