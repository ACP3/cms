<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Filters;

use ACP3\Core;

class PageCssClasses extends AbstractFilter
{
    /**
     * @var \ACP3\Core\Assets\PageCssClasses
     */
    protected $pageCssClasses;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var string
     */
    protected $cssClassCache = '';

    /**
     * @param \ACP3\Core\Assets\PageCssClasses $pageCssClasses
     * @param \ACP3\Core\Http\RequestInterface $request
     */
    public function __construct(
        Core\Assets\PageCssClasses $pageCssClasses,
        Core\Http\RequestInterface $request
    ) {
        $this->pageCssClasses = $pageCssClasses;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(string $tplOutput, \Smarty_Internal_Template $smarty)
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
    protected function buildPageCssClasses()
    {
        $pieces = [
            $this->pageCssClasses->getModule(),
            $this->pageCssClasses->getControllerAction(),
        ];

        if ($this->request->getArea() === Core\Controller\AreaEnum::AREA_ADMIN) {
            $pieces[] = 'in-admin';
        } elseif ($this->request->isHomepage() === true) {
            $pieces[] = 'is-homepage';
        } else {
            $pieces[] = $this->pageCssClasses->getDetails();
        }

        return $pieces;
    }
}
