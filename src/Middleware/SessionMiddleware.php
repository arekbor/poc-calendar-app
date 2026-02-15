<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SessionMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => filter_var($_ENV['SESSION_LIFETIME'], FILTER_VALIDATE_INT, FILTER_THROW_ON_FAILURE),
                'domain' => filter_var($_ENV['SESSION_DOMAIN'], FILTER_VALIDATE_DOMAIN, [
                    'flags' => FILTER_FLAG_HOSTNAME | FILTER_THROW_ON_FAILURE
                ]),
                'path' => $_ENV['SESSION_PATH'],
                'secure' => filter_var($_ENV['SESSION_SECURE'], FILTER_VALIDATE_BOOL, FILTER_THROW_ON_FAILURE),
                'httponly' => filter_var($_ENV['SESSION_HTTPONLY'], FILTER_VALIDATE_BOOL, FILTER_THROW_ON_FAILURE),
                'samesite' => $_ENV['SESSION_SAMESITE'],
            ]);

            if (!session_start()) {
                throw new \RuntimeException('Failed to start session.');
            }
        }

        //SESSION REGENERATION
        if(!isset($_SESSION['session_last_regeneration'])) {
            $_SESSION['session_last_regeneration'] = time();
        }

        $regenerationInterval = filter_var($_ENV['SESSION_REGENERATION_INTERVAL'], FILTER_VALIDATE_INT, FILTER_THROW_ON_FAILURE);
        
        if (time() - $_SESSION['session_last_regeneration'] >= $regenerationInterval) {
            session_regenerate_id(true);
            $_SESSION['session_last_regeneration'] = time();
        }

        return $handler->handle($request);
    }
}