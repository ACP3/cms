<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core;
use ACP3\Core\Controller\Event\CustomTemplateVariableEvent;

abstract class AbstractFrontendAction extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(Context\FrontendContext $context)
    {
        parent::__construct($context);

        $this->eventDispatcher = $context->getEventDispatcher();
    }

    /**
     * {@inheritdoc}
     */
    protected function addCustomTemplateVarsBeforeOutput()
    {
        $this->eventDispatcher->dispatch(
            new CustomTemplateVariableEvent($this->view),
            CustomTemplateVariableEvent::NAME
        );
    }

    /**
     * @return $this
     */
    public function setLayout(string $layout)
    {
        $this->view->setLayout($layout);

        return $this;
    }
}
