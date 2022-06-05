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

    public function __construct(private readonly EventDispatcherInterface $eventDispatcher)
    {
    }

    public function __invoke(string $tplOutput, \Smarty_Internal_Template $smarty): string
    {
        if (str_contains($tplOutput, '<body')) {
            if ($this->svgIcons === null) {
                $this->addSvgIcons();
            }

            try {
                $tplOutput = str_replace('<body', '<body data-svg-icons="' . htmlspecialchars(json_encode($this->svgIcons, JSON_THROW_ON_ERROR), ENT_QUOTES) . '"', $tplOutput);
            } catch (\Throwable) {
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
