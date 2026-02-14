<?php

declare(strict_types=1);

use App\Middleware\DefaultErrorHandler;
use App\Middleware\SessionMiddleware;
use Slim\App;
use Slim\Csrf\Guard;

return function (App $app): void {
    $app->addRoutingMiddleware();
    $app->addBodyParsingMiddleware();

    $app->add(Guard::class);
    
    $app->add(SessionMiddleware::class);

    $errorMiddleware = $app->addErrorMiddleware(false, false, false);
    $errorMiddleware->setDefaultErrorHandler(DefaultErrorHandler::class);
};