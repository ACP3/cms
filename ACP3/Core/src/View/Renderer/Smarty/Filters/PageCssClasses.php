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
    private $pageCssClasses;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var string
     */
    private $cssClassCache = '';

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
    public function __invoke(string $tplOutput, \Smarty_Internal_Template $smarty): string
    {
        if (\strpos($tplOutput, '<body') !== false) {
            if ($this->cssClassCache === '') {
                $this->cssClassCache = 'class="' . \implode(' ', $this->buildPageCssClasses()) . '"';
            }

            $tplOutput = \str_replace('<body', '<body ' . $this->cssClassCache, $tplOutput);
        }

        return $tplOutput;
    }

    private function buildPageCssClasses(): array
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
