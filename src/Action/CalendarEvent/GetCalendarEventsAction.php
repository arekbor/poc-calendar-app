<?php

declare(strict_types=1);

namespace App\Action\CalendarEvent;

use App\Action\AbstractAction;
use App\Repository\CalendarEventRepository;
use Fig\Http\Message\StatusCodeInterface;
use Respect\Validation\Rules\Date;
use Respect\Validation\Validator;
use Slim\Http\Response;

final class GetCalendarEventsAction extends AbstractAction
{
    public function __construct(
        private readonly CalendarEventRepository $calendarEventRepository,
    ) {
    }

    public function handleAction(): Response
    {
        $weekStart = $this->getArgs()['weekStart'];
        $weekEnd = $this->getArgs()['weekEnd'];

        $dateValidator = new Validator(
            new Date()
        );

        if (!$dateValidator->isValid($weekStart)) {
            throw new \InvalidArgumentException("Week start is not valid.");
        }

        if (!$dateValidator->isValid($weekEnd)) {
            throw new \InvalidArgumentException("Week end is not valid.");
        }

        $utc = new \DateTimeZone('UTC');
        $weekStart = new \DateTimeImmutable($weekStart)->setTimezone($utc);
        $weekStart = $weekStart->setTime(0, 0, 0);

        $weekEnd = new \DateTimeImmutable($weekEnd)->setTimezone($utc);
        $weekEnd = $weekEnd->setTime(24, 0, 0);

        if ($weekStart >= $weekEnd) {
            throw new \InvalidArgumentException("Week start should be earlier than week end");
        }

        $result = $this->calendarEventRepository->getCalendarEventsInRange([
            'start_date' => $weekStart->format(\DateTime::ATOM),
            'end_date' => $weekEnd->format(\DateTime::ATOM)
        ]);

        return $this->getResponse()->withJson($result)->withStatus(StatusCodeInterface::STATUS_OK);
    }
}