<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\Assets\Renderer\CSSRenderer;

class MoveToHead extends AbstractMoveElementFilter
{
    public const ELEMENT_CATCHER_REGEX_PATTERN = '!@@@SMARTY:STYLESHEETS:BEGIN@@@(.*?)@@@SMARTY:STYLESHEETS:END@@@!is';
    protected const PLACEHOLDER = '<!-- STYLESHEETS -->';

    public function __construct(private CSSRenderer $CSSRenderer)
    {
    }

    protected function addElementFromMinifier(): string
    {
        return $this->CSSRenderer->renderHtmlElement();
    }

    public function __invoke(string $tplOutput, \Smarty_Internal_Template $smarty): string
    {
        if (str_contains($tplOutput, (string) static::PLACEHOLDER)) {
            $tplOutput = str_replace(
                static::PLACEHOLDER,
                $this->addElementFromMinifier() . $this->addElementsFromTemplates($tplOutput),
                $this->getCleanedUpTemplateOutput($tplOutput)
            );
        }

        return $tplOutput;
    }
}
