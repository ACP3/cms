<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\Event;

use ACP3\Core\Http\RequestInterface;
use Symfony\Component\HttpFoundation\Response;

class ControllerActionAfterDispatchEvent extends ControllerActionBeforeDispatchEvent
{
    public const NAME = 'core.application.controller_action_dispatcher.after_dispatch';

    public function __construct(string $serviceId, private RequestInterface $request, private Response $response)
    {
        parent::__construct($serviceId);
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
