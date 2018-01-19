<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

use Symfony\Component\HttpFoundation\Response;

interface ActionInterface
{
    /**
     * @return $this
     */
    public function preDispatch();

    /**
     * Gets a class from the service container.
     *
     * @param string $serviceId
     *
     * @return mixed
     */
    public function get($serviceId);

    /**
     * @param Response|string|array $actionResult
     *
     * @return Response
     */
    public function display($actionResult);
}
