<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\Event;

use ACP3\Core\Http\RequestInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ControllerActionRequestEvent extends Event
{
    public const NAME = 'core.application.controller_action_dispatcher.request';

    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
