<?php

declare(strict_types=1);

namespace App\Action\Auth;

use App\Action\AbstractAction;
use Fig\Http\Message\StatusCodeInterface;
use Slim\Http\Response;

final class MeAction extends AbstractAction
{
    public function handleAction(): Response
    {
        return $this->getResponse()->withJson($_SESSION['user'], StatusCodeInterface::STATUS_OK);
    }
}