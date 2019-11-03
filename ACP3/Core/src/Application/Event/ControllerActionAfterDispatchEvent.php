<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\Event;

use Symfony\Component\HttpFoundation\Response;

class ControllerActionAfterDispatchEvent extends ControllerActionBeforeDispatchEvent
{
    public const NAME = 'core.application.controller_action_dispatcher.after_dispatch';
    /**
     * @var Response
     */
    private $response;

    /**
     * ControllerActionDispatchAfterEvent constructor.
     */
    public function __construct(string $serviceId, Response $response)
    {
        parent::__construct($serviceId);

        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}