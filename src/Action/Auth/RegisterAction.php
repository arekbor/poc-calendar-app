<?php

declare(strict_types=1);

namespace App\Action\Auth;

use App\Action\AbstractAction;
use App\Exception\UnprocessableEntityException;
use App\Repository\AppUserRepository;
use Fig\Http\Message\StatusCodeInterface;
use Respect\Validation\Rules\Alnum;
use Respect\Validation\Rules\Email;
use Respect\Validation\Rules\Length;
use Respect\Validation\Rules\NotEmpty;
use Respect\Validation\Rules\NoWhitespace;
use Respect\Validation\Rules\StringType;
use Respect\Validation\Validator;
use Slim\Http\Response;

final class RegisterAction extends AbstractAction
{
    public function __construct(
        private readonly AppUserRepository $appUserRepository
    ) {
    }

    public function handleAction(): Response
    {
        $body = $this->getParsedBodyAsArray();

        $username = trim((string) ($body['username'] ?? ''));
        $email = mb_strtolower(trim((string) ($body['email'] ?? '')));
        $password = (string) ($body['password'] ?? '');
        $repeatPassword = (string) ($body['repeat_password'] ?? '');

        $usernameValidator = new Validator(
            new NotEmpty(),
            new StringType(),
            new Alnum(),
            new NoWhitespace(),
            new Length(5, 20)
        );

        if (!$usernameValidator->isValid($username)) {
            throw new UnprocessableEntityException(
                'Username must be 5–20 characters long and contain only alphanumeric characters.'
            );
        }

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
            new StringType(),
            new Length(8, null)
        );

        if (!$passwordValidator->isValid($password)) {
            throw new UnprocessableEntityException(
                'Password must be at least 8 characters long.'
            );
        }

        if ($password !== $repeatPassword) {
            throw new UnprocessableEntityException(
                'Password and repeat password must be equal.'
            );
        }

        if ($this->appUserRepository->isUserWithEmailExists($email)) {
            throw new UnprocessableEntityException('Email already exists.');
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $userId = $this->appUserRepository->create([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
        ]);

        if ($userId <= 0) {
            throw new \RuntimeException('Error while creating user.');
        }

        return $this->getResponse()
            ->withJson(['userId' => $userId], StatusCodeInterface::STATUS_CREATED);
    }
}