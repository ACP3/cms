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

    /**
     * @var Response
     */
    private $response;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;

    public function __construct(string $serviceId, RequestInterface $request, Response $response)
    {
        parent::__construct($serviceId);

        $this->response = $response;
        $this->request = $request;
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
