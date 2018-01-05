<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\View\Event\TemplateEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Event extends AbstractFunction
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Event constructor.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return string
     */
    public function getExtensionName()
    {
        return 'event';
    }

    /**
     * @param array                     $params
     * @param \Smarty_Internal_Template $smarty
     *
     * @return string
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        if (isset($params['name'])) {
            \ob_start();
            $this->eventDispatcher->dispatch($params['name'], new TemplateEvent($this->parseArguments($params)));
            $result = \ob_get_contents();
            \ob_end_clean();

            return $result;
        }

        return '';
    }

    /**
     * @param array $arguments
     *
     * @return array
     */
    protected function parseArguments(array $arguments)
    {
        unset($arguments['name']);

        return $arguments;
    }
}
