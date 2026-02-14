<?php

declare(strict_types=1);

namespace App\Action;

use DI\Attribute\Inject;
use Fig\Http\Message\StatusCodeInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Slim\Views\PhpRenderer;

abstract class AbstractAction
{
    private ServerRequest $request;
    private Response $response;
    private array $args;

    private PhpRenderer $phpRenderer;

    #[Inject()]
    public function setPhpRenderer(PhpRenderer $phpRenderer): void
    {
        $this->phpRenderer = $phpRenderer;
    }

    public function __invoke(
        ServerRequest $request,
        Response $response,
        array $args
    ): Response {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        return $this->handleAction();
    }

    protected function render(string $template, array $data = [], int $code = StatusCodeInterface::STATUS_OK): Response
    {
        $response = $this->getResponse()->withStatus($code);
        return $this->phpRenderer->render($response, $template, $data);
    }

    protected function getRequest(): ServerRequest
    {
        return $this->request;
    }

    protected function getResponse(): Response
    {
        return $this->response;
    }

    protected function getParsedBodyAsArray(): array
    {
        $body = $this->getRequest()->getParsedBody();

        if (!is_array($body)) {
            throw new \RuntimeException("Invalid request body");
        }

        return $body;
    }

    protected function getArgs(): array
    {
        return $this->args;
    }

    abstract public function handleAction(): Response;
}