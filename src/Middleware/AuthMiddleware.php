<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\UnauthorizedException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new UnauthorizedException();
        }

        $userId = $_SESSION['user']['id'] ?? null;
        
        if (empty($userId)) {
            throw new UnauthorizedException();
        }

        if (!is_int($userId) && !ctype_digit((string)$userId)) {
            throw new UnauthorizedException();
        }

        return $handler->handle($request);
    }
}