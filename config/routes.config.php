<?php

declare(strict_types=1);

use App\Action\Auth\LoginAction;
use App\Action\Auth\LogoutAction;
use App\Action\Auth\MeAction;
use App\Action\Auth\RegisterAction;
use App\Action\CalendarEvent\CreateCalendarEventAction;
use App\Action\CalendarEvent\DeleteCalendarEventAction;
use App\Action\CalendarEvent\GetCalendarEventsAction;
use App\Action\CalendarEvent\UpdateCalendarEventAction;
use App\Action\CalendarEvent\UpdatePermissionsAction;
use App\Action\CsrfTokenAction;
use App\Action\IndexAction;
use App\Middleware\AuthMiddleware;
use App\Middleware\CanDeleteCalendarEventMiddleware;
use Slim\Interfaces\RouteCollectorProxyInterface;

return function (RouteCollectorProxyInterface $route): void {
    $route->get('/{vuePath:(?!api).*}', IndexAction::class);

    $route->group('/api', function(RouteCollectorProxyInterface $route): void {
        $route->get('/csrf-token', CsrfTokenAction::class);

        $route->group('/auth', function(RouteCollectorProxyInterface $route): void {
            $route->post('/register', RegisterAction::class);
            $route->post('/login', LoginAction::class);
            $route->post('/logout', LogoutAction::class);

            $route->get('/me', MeAction::class)
                ->add(AuthMiddleware::class);
        });

        $route->group('/calendarEvent', function(RouteCollectorProxyInterface $route): void {
            $route->get('/{weekStart}/{weekEnd}', GetCalendarEventsAction::class);
            $route->post('', CreateCalendarEventAction::class);
            $route->delete('/{id}', DeleteCalendarEventAction::class)
                ->add(CanDeleteCalendarEventMiddleware::class)
            ;

            $route->put('/updatePermissions', UpdatePermissionsAction::class);

            $route->put('', UpdateCalendarEventAction::class);

        })->add(AuthMiddleware::class);
    });
};