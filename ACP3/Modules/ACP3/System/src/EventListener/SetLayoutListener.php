<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\EventListener;

use ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\View;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SetLayoutListener implements EventSubscriberInterface
{
    public function __construct(private RequestInterface $request, private View $view)
    {
    }

    public function __invoke()
    {
        if ($this->request->getArea() === AreaEnum::AREA_WIDGET) {
            return;
        }

        if ($this->request->isXmlHttpRequest()) {
            $paths = $this->fetchLayoutPaths('layout.ajax', 'System/layout.ajax.tpl');
        } else {
            $paths = $this->fetchLayoutPaths('layout', 'System/layout.tpl');
        }

        $this->iterateOverLayoutPaths($paths);
    }

    private function fetchLayoutPaths(string $layoutFileName, string $defaultLayoutName): array
    {
        return [
            $this->request->getModule() . '/' . $this->request->getArea() . '/' . $layoutFileName . '.' . $this->request->getController() . '.' . $this->request->getAction() . '.tpl',
            $this->request->getModule() . '/' . $this->request->getArea() . '/' . $layoutFileName . '.' . $this->request->getController() . '.tpl',
            $this->request->getModule() . '/' . $this->request->getArea() . '/' . $layoutFileName . '.tpl',
            $this->request->getModule() . '/' . $layoutFileName . '.tpl',
            $defaultLayoutName,
        ];
    }

    /**
     * @param string[] $paths
     */
    private function iterateOverLayoutPaths(array $paths): void
    {
        foreach ($paths as $path) {
            if ($this->view->templateExists($path)) {
                $this->view->setLayout($path);

                break;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ControllerActionBeforeDispatchEvent::NAME => '__invoke',
        ];
    }
}
