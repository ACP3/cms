<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Core\View\Renderer\Smarty\Filters;

use ACP3\Core;
use ACP3\Core\View\Renderer\Smarty\Filters\AbstractFilter;

class PageCssClasses extends AbstractFilter
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var string
     */
    private $cssClassCache = '';

    /**
     * @param \ACP3\Core\Http\RequestInterface $request
     */
    public function __construct(
        Core\Http\RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionName()
    {
        return 'output';
    }

    /**
     * {@inheritdoc}
     */
    public function process($tplOutput, \Smarty_Internal_Template $smarty)
    {
        if (\strpos($tplOutput, '<body') !== false) {
            if ($this->cssClassCache === '') {
                $this->cssClassCache = 'class="' . \implode(' ', $this->buildPageCssClasses()) . '"';
            }

            $tplOutput = \str_replace('<body', '<body ' . $this->cssClassCache, $tplOutput);
        }

        return $tplOutput;
    }

    /**
     * @return array
     */
    protected function buildPageCssClasses(): array
    {
        $pieces = [
            $this->request->getModule(),
            $this->request->getController(),
        ];

        if ($this->request->getArea() === Core\Controller\AreaEnum::AREA_ADMIN) {
            $pieces[] = 'in-admin';
        } elseif ($this->request->isHomepage() === true) {
            $pieces[] = 'is-homepage';
        }

        return $pieces;
    }
}
