<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\ForbiddenException;
use App\Exception\UnauthorizedException;
use App\Exception\UnprocessableEntityException;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Http\Response;

final class DefaultErrorHandler
{
    public function __construct(
        private readonly DecoratedResponseFactory $decoratedResponseFactory
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        \Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): Response {
        $data = [
            'message' => $exception->getMessage()
        ];
        
        if ($exception instanceof UnprocessableEntityException) {
            return $this->decoratedResponseFactory
                ->createResponse(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY)
                ->withJson($data)
            ;

        } elseif ($exception instanceof UnauthorizedException) {
            return $this->decoratedResponseFactory
                ->createResponse(StatusCodeInterface::STATUS_UNAUTHORIZED)
                ->withJson($data)
            ;
        } elseif($exception instanceof ForbiddenException) {
            return $this->decoratedResponseFactory
                ->createResponse(StatusCodeInterface::STATUS_FORBIDDEN)
                ->withJson($data)
            ;
        } else {
            if ($_ENV['APP_ENV'] === 'dev') throw $exception;
        }

        return $this->decoratedResponseFactory
            ->createResponse(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR)
        ;
    }
}