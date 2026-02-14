<?php

declare(strict_types=1);

namespace App\Action\CalendarEvent;

use App\Action\AbstractAction;
use App\Repository\CalendarEventRepository;
use Fig\Http\Message\StatusCodeInterface;
use Slim\Http\Response;

final class DeleteCalendarEventAction extends AbstractAction
{
    public function __construct(
        private readonly CalendarEventRepository $calendarEventRepository
    ) {
    }

    public function handleAction(): Response
    {
        $id = $this->getRequest()->getAttribute('id', null);
        if ($id === null) {
            throw new \Exception("Id attribute not found");
        }

        $id = intval($id);
        $this->calendarEventRepository->delete($id);

        return $this->getResponse()->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);
    }
}