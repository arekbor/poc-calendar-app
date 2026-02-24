<?php

declare(strict_types=1);

namespace App\Action\CalendarEvent;

use App\Action\AbstractAction;
use App\Exception\UnprocessableEntityException;
use App\Repository\CalendarEventRepository;
use Fig\Http\Message\StatusCodeInterface;
use Respect\Validation\Rules\DateTime;
use Respect\Validation\Rules\NotEmpty;
use Respect\Validation\Rules\StringType;
use Respect\Validation\Validator;
use Slim\Http\Response;

final class CreateCalendarEventAction extends AbstractAction
{
    public function __construct(
        private readonly CalendarEventRepository $calendarEventEepository
    ) {
    }

    public function handleAction(): Response
    {
        $body = $this->getParsedBodyAsArray();

        $titleValidator = new Validator(
            new NotEmpty(),
            new StringType()
        );

        if(!$titleValidator->isValid($body['title'])) {
            throw new UnprocessableEntityException('Title is required.');
        }

        $dateValidator = new Validator(
            new DateTime()
        );

        if (!$dateValidator->isValid($body['start_date'])) {
            throw new UnprocessableEntityException('Start format is not valid.');
        }

        if (!$dateValidator->isValid($body['end_date'])) {
            throw new UnprocessableEntityException('End format is not valid.');
        }

        //TODO: teoretycznie to nie jest potrzebne
        $utc = new \DateTimeZone('UTC');
        $start = new \DateTimeImmutable($body['start_date'])->setTimezone($utc);
        $end = new \DateTimeImmutable($body['end_date'])->setTimezone($utc);

        if ($start >= $end) {
            throw new UnprocessableEntityException('Start date should be earlier than end date.');
        }

        //TODO: event nie moze pokrywać się z istniejącym!

        $calendarEvent = [
            'title' => $body['title'],
            'start_date' => $start->format(\DateTime::ATOM),
            'end_date' => $end->format(\DateTime::ATOM),
            'color' => $body['color']
        ];

        $lastInsertId = $this->calendarEventEepository->create($calendarEvent);
        $calendarEvent['id'] = $lastInsertId;

        return $this->getResponse()->withJson($calendarEvent, StatusCodeInterface::STATUS_CREATED);
    }
}