<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\View\Event\TemplateEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Event
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
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
     * @return mixed
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        if (isset($params['name'])) {
            $this->eventDispatcher->dispatch($params['name'], new TemplateEvent($this->parseArguments($params)));
        }
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