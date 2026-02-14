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

final class UpdateCalendarEventAction extends AbstractAction
{
    public function __construct(
        private readonly CalendarEventRepository $calendarEventRepository
    ) {
        
    }

    public function handleAction(): Response
    {
        $body = $this->getParsedBodyAsArray();

        $calendarEvent = $this->calendarEventRepository->get(intval($body['id']));
        if($calendarEvent === null) {
            throw new \Exception('Calendar event not found');
        }

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

        $calendarEvent['title'] = $body['title'];
        $calendarEvent['start_date'] = $start->format(\DateTime::ATOM);
        $calendarEvent['end_date'] = $end->format(\DateTime::ATOM);
        $calendarEvent['color'] = $body['color'];

        $this->calendarEventRepository->update($calendarEvent);
        
        return $this->getResponse()->withJson($calendarEvent)->withStatus(StatusCodeInterface::STATUS_OK);
    }
}   