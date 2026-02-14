<?php

declare(strict_types=1);

namespace App\Action;

use Slim\Csrf\Guard;
use Slim\Http\Response;

final class CsrfTokenAction extends AbstractAction
{
    public function __construct(
        private readonly Guard $guard
    ) {
    }

    public function handleAction(): Response
    {
        return $this->getResponse()->withJson($this->guard->generateToken());
    }
}