<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller\Context;

use ACP3\Core;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FrontendContext extends Core\Controller\Context\WidgetContext
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct(
            $context->getContainer(),
            $context->getTranslator(),
            $context->getRequest(),
            $context->getView(),
            $context->getConfig(),
            $context->getAppPath(),
            $context->getResponse()
        );

        $this->eventDispatcher = $eventDispatcher;
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }
}
