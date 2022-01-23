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
    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(array $params, \Smarty_Internal_Template $smarty): string
    {
        if (isset($params['name'])) {
            $event = new TemplateEvent($this->parseArguments($params));
            $this->eventDispatcher->dispatch($event, $params['name']);

            return $event->getContent();
        }

        throw new \InvalidArgumentException('Could have to call the {event} Smarty function with the argument "name", which specifies the name of the event');
    }

    /**
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>
     */
    protected function parseArguments(array $arguments): array
    {
        unset($arguments['name']);

        return $arguments;
    }
}
