<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\View;

trait LayoutAwareControllerTrait
{
    /**
     * @var string
     */
    private $layout = 'layout.tpl';

    /**
     * @return string
     */
    public function getLayout(): string
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     * @return $this
     */
    public function setLayout(string $layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * @param string $defaultAjaxLayoutName
     * @param string $defaultLayoutName
     * @return string
     */
    protected function fetchLayoutViaInheritance(
        string $defaultAjaxLayoutName = 'System/layout.ajax.tpl',
        string $defaultLayoutName = 'layout.tpl'
    ): string {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $paths = $this->fetchLayoutPaths('layout.ajax', $defaultAjaxLayoutName);
        } else {
            $paths = $this->fetchLayoutPaths('layout', $defaultLayoutName);
        }

        $this->iterateOverLayoutPaths($paths);

        return $this->getLayout();
    }

    /**
     * @param string $layoutFileName
     * @param string $defaultLayoutName
     * @return array
     */
    private function fetchLayoutPaths(string $layoutFileName, string $defaultLayoutName): array
    {
        return [
            $this->getRequest()->getModule() . '/' . $this->getRequest()->getArea() . '/' . $layoutFileName . '.' . $this->getRequest()->getController() . '.' . $this->getRequest()->getAction() . '.tpl',
            $this->getRequest()->getModule() . '/' . $this->getRequest()->getArea() . '/' . $layoutFileName . '.' . $this->getRequest()->getController() . '.tpl',
            $this->getRequest()->getModule() . '/' . $this->getRequest()->getArea() . '/' . $layoutFileName . '.tpl',
            $this->getRequest()->getModule() . '/' . $layoutFileName . '.tpl',
            $defaultLayoutName,
        ];
    }

    /**
     * @param array $paths
     */
    private function iterateOverLayoutPaths(array $paths)
    {
        if ($this->getLayout() !== 'layout.tpl') {
            return;
        }

        foreach ($paths as $path) {
            if ($this->getView()->templateExists($path)) {
                $this->setLayout($path);

                break;
            }
        }
    }

    /**
     * @return RequestInterface
     */
    abstract protected function getRequest(): RequestInterface;

    /**
     * @return View
     */
    abstract protected function getView(): View;
}
