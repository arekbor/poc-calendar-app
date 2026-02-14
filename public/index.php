<?php

declare(strict_types=1);

use App\Repository\DatabaseSchema;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\ResponseEmitter;
use Symfony\Component\Dotenv\Dotenv;

define('BASE_PATH', dirname(__DIR__));
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('TEMPLATES_BASE_PATH', BASE_PATH . '/resources/templates');

require_once BASE_PATH . '/vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->loadEnv(BASE_PATH . '/.env');

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAttributes(true);

$dependenciesConfig = require_once CONFIG_PATH . '/dependencies.config.php';
$dependenciesConfig($containerBuilder);

$container = $containerBuilder->build();

$app = AppFactory::createFromContainer($container);

$routesConfig = require_once CONFIG_PATH . '/routes.config.php';
$routesConfig($app);


// $container->set(RouteParserInterface::class, fn() => $app->getRouteCollector()->getRouteParser());
// $responseFactory = $app->getResponseFactory();
// $container->set('csrf', fn() => new Guard($responseFactory));

$middlewareConfig = require_once CONFIG_PATH . '/middleware.config.php';
$middlewareConfig($app);



//TODO: usun to, zrob jakąs komende czy cos :)
$databaseSchema = new DatabaseSchema();
$databaseSchema->createTables();


$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();


$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
