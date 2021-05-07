<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\View\Renderer\Smarty\Filters\Event\JsSvgIconEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class JsSvgIcons extends AbstractFilter
{
    /**
     * @var array<string, string>|null
     */
    private $svgIcons;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(string $tplOutput, \Smarty_Internal_Template $smarty): string
    {
        if (strpos($tplOutput, '<body') !== false) {
            if ($this->svgIcons === null) {
                $this->addSvgIcons();
            }

            try {
                $tplOutput = str_replace('<body', '<body data-svg-icons="' . htmlspecialchars(json_encode($this->svgIcons, JSON_THROW_ON_ERROR), ENT_QUOTES) . '"', $tplOutput);
            } catch (\Throwable $e) {
                // Intentionally omitted
            }
        }

        return $tplOutput;
    }

    private function addSvgIcons(): void
    {
        $event = $this->eventDispatcher->dispatch(new JsSvgIconEvent());

        $this->svgIcons = $event->getIcons();
    }
}
