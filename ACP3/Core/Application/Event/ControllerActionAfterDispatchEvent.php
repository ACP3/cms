<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\Event;

use Symfony\Component\HttpFoundation\Response;

class ControllerActionAfterDispatchEvent extends ControllerActionBeforeDispatchEvent
{
    /**
     * @var Response
     */
    private $response;

    /**
     * ControllerActionDispatchAfterEvent constructor.
     *
     * @param string   $serviceId
     * @param Response $response
     */
    public function __construct($serviceId, Response $response)
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
