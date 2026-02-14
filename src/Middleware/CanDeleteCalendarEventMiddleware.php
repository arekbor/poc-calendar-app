<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\ForbiddenException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CanDeleteCalendarEventMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new ForbiddenException();
        }

        $canDeleteCalendarEvent = $_SESSION['user']['calendar']['canDelete'] ?? null;
        if (!is_bool($canDeleteCalendarEvent) || !$canDeleteCalendarEvent) {
            throw new ForbiddenException();
        }

        return $handler->handle($request);
    }
}