<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\Event;

use ACP3\Core\Http\RequestInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class ControllerActionRequestEvent extends Event
{
    // @deprecated since 6.11.0, use `ControllerActionRequestEvent::class` instead
    public const NAME = 'core.application.controller_action_dispatcher.request';

    private ?Response $response = null;

    public function __construct(private readonly RequestInterface $request)
    {
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function hasResponse(): bool
    {
        return $this->response !== null;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}
