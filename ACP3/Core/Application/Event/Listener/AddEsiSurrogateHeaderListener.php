<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\Event\Listener;

use ACP3\Core\Application\Event\ControllerActionAfterDispatchEvent;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AddEsiSurrogateHeaderListener
{
    /**
     * @var Request
     */
    private $request;

    /**
     * AddEsiSurrogateHeaderListener constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param ControllerActionAfterDispatchEvent $event
     */
    public function execute(ControllerActionAfterDispatchEvent $event)
    {
        $response = $event->getResponse();

        if ($this->isExcludedFromEsi($response)) {
            return;
        }

        $response->headers->set('Surrogate-Control', 'content="ESI/1.0"');
    }

    /**
     * @param Response $response
     * @return bool
     */
    private function isExcludedFromEsi(Response $response)
    {
        return $this->request->getArea() === AreaEnum::AREA_WIDGET
        || $response instanceof BinaryFileResponse
        || $response instanceof StreamedResponse;
    }
}
