<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Filters;

use ACP3\Core;

class PageCssClasses extends AbstractFilter
{
    private string $cssClassCache = '';

    public function __construct(private readonly Core\Assets\PageCssClasses $pageCssClasses, private readonly Core\Http\RequestInterface $request)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(string $tplOutput, \Smarty_Internal_Template $smarty): string
    {
        if (str_contains($tplOutput, '<body')) {
            if ($this->cssClassCache === '') {
                $this->cssClassCache = 'class="' . implode(' ', $this->buildPageCssClasses()) . '"';
            }

            $tplOutput = str_replace('<body', '<body ' . $this->cssClassCache, $tplOutput);
        }

        return $tplOutput;
    }

    /**
     * @return string[]
     */
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
