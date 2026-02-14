<?php

declare(strict_types=1);

namespace App\Action\Auth;

use App\Action\AbstractAction;
use Fig\Http\Message\StatusCodeInterface;
use Slim\Http\Response;

final class LogoutAction extends AbstractAction
{
    public function handleAction(): Response
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();

            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();

                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }

            session_destroy();
        }

        return $this->getResponse()->withJson([], StatusCodeInterface::STATUS_NO_CONTENT);
    }
}