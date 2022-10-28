<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\Assets\PageCssClasses as PageCssClassesHelper;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use Masterminds\HTML5;

class PageCssClasses extends AbstractFilter
{
    private string $cssClassCache = '';

    public function __construct(private readonly PageCssClassesHelper $pageCssClasses, private readonly RequestInterface $request)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(string $tplOutput, \Smarty_Internal_Template $smarty): string
    {
        if (str_contains($tplOutput, '<body')) {
            $html5 = new HTML5();
            $dom = $html5->loadHTML($tplOutput);
            $body = $dom->documentElement->lastElementChild;

            if ($this->cssClassCache === '') {
                $this->cssClassCache = implode(
                    ' ',
                    array_filter(
                        [$body->getAttribute('class'), ...$this->buildPageCssClasses()],
                        static fn ($item) => $item !== ''
                    )
                );
            }
            $body->setAttribute('class', $this->cssClassCache);

            $tplOutput = $html5->saveHTML($dom);
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

        if ($this->request->getArea() === AreaEnum::AREA_ADMIN) {
            $pieces[] = 'in-admin';
        } elseif ($this->request->isHomepage() === true) {
            $pieces[] = 'is-homepage';
        } else {
            $pieces[] = $this->pageCssClasses->getDetails();
        }

        return $pieces;
    }
}
