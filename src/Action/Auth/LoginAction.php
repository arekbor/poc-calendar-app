<?php

declare(strict_types=1);

namespace App\Action\Auth;

use App\Action\AbstractAction;
use App\Exception\UnprocessableEntityException;
use App\Repository\AppUserRepository;
use Fig\Http\Message\StatusCodeInterface;
use Respect\Validation\Rules\Email;
use Respect\Validation\Rules\NotEmpty;
use Respect\Validation\Rules\NoWhitespace;
use Respect\Validation\Rules\StringType;
use Respect\Validation\Validator;
use Slim\Http\Response;

final class LoginAction extends AbstractAction
{
    public function __construct(
        private readonly AppUserRepository $appUserRepository
    ) {
    }

    public function handleAction(): Response
    {
        $body = $this->getParsedBodyAsArray();

        $email = mb_strtolower(trim((string) ($body['email'] ?? '')));
        $password = (string) ($body['password'] ?? '');

        $emailValidator = new Validator(
            new NotEmpty(),
            new StringType(),
            new Email(),
            new NoWhitespace()
        );

        if (!$emailValidator->isValid($email)) {
            throw new UnprocessableEntityException('Invalid email format.');
        }

        $passwordValidator = new Validator(
            new NotEmpty(),
            new StringType()
        );

        if (!$passwordValidator->isValid($password)) {
            throw new UnprocessableEntityException(
                'Password must be not empty.'
            );
        }

        $user = $this->appUserRepository->getByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            throw new UnprocessableEntityException('Invalid email or password');
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'calendar' => [
                'canDelete' => true,
            ]
        ];

        return $this->getResponse()->withJson($_SESSION['user'], StatusCodeInterface::STATUS_OK);
    }
}