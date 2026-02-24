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

        $utc = new \DateTimeZone('UTC');
        $start = new \DateTimeImmutable($body['start_date'])->setTimezone($utc);
        $end = new \DateTimeImmutable($body['end_date'])->setTimezone($utc);

        if ($start >= $end) {
            throw new UnprocessableEntityException('Start date should be earlier than end date.');
        }

        if (!$body['isRecurring']) {
            //TODO: check if event exists in range
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

        //convert to local tz because Daylight saving time!
        $localTz = new \DateTimeZone('Europe/Warsaw');
        $utcTz   = new \DateTimeZone('UTC');

        $startLocal = new \DateTimeImmutable($body['start_date'])->setTimezone($localTz);
        $endLocal = new \DateTimeImmutable($body['end_date'])->setTimezone($localTz);

        $endOfYear = (clone $startLocal)->modify('last day of December this year');
        $interval = new \DateInterval('P1W');
        $duration = $startLocal->diff($endLocal);

        $period = new \DatePeriod($startLocal, $interval, $endOfYear);

        foreach ($period as $eventStartLocal) {
            $eventEndLocal = (clone $eventStartLocal)->add($duration);

            // konwersja DO UTC dopiero przy zapisie
            $eventStartUtc = $eventStartLocal->setTimezone($utcTz);
            $eventEndUtc   = $eventEndLocal->setTimezone($utcTz);

            $calendarEvent = [
                'title' => $body['title'],
                'start_date' => $eventStartUtc->format(\DateTime::ATOM),
                'end_date'   => $eventEndUtc->format(\DateTime::ATOM),
                'color' => $body['color']
            ];

            $this->calendarEventEepository->create($calendarEvent);
        }

        return $this->getResponse();
    }
}