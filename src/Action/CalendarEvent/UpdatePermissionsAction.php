<?php

declare(strict_types=1);

namespace App\Action\CalendarEvent;

use App\Action\AbstractAction;
use Slim\Http\Response;

final class UpdatePermissionsAction extends AbstractAction
{
    public function handleAction(): Response
    {
        $permissions = $this->getRequest()->getParsedBody();

        dd($permissions);

        throw new \Exception('Not implemented');
    }
}