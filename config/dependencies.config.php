<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Csrf\Guard;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Views\PhpRenderer;

return function(ContainerBuilder $builder): void {
    $definitions = [];

    $definitions[PhpRenderer::class] = fn() 
        => new PhpRenderer(TEMPLATES_BASE_PATH);

    $definitions[Guard::class] = fn(ResponseFactory $responseFactory) 
        => new Guard($responseFactory);

    $definitions[DecoratedResponseFactory::class] = fn(ResponseFactory $responseFactory, StreamFactory $streamFactory) 
        => new DecoratedResponseFactory($responseFactory, $streamFactory);

    $builder->addDefinitions($definitions);
};