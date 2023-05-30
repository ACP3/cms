<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\Assets\Renderer\JavaScriptRenderer;

class MoveToBottom extends AbstractMoveElementFilter
{
    public const ELEMENT_CATCHER_REGEX_PATTERN = '!@@@SMARTY:JAVASCRIPTS:BEGIN@@@(.*?)@@@SMARTY:JAVASCRIPTS:END@@@!is';
    protected const PLACEHOLDER = '<!-- JAVASCRIPTS -->';

    public function __construct(private readonly JavaScriptRenderer $javaScriptRenderer)
    {
    }

    public function __invoke(string $tplOutput, \Smarty_Internal_Template $smarty): string
    {
        if (str_contains($tplOutput, (string) static::PLACEHOLDER)) {
            return str_replace(
                static::PLACEHOLDER,
                $this->javaScriptRenderer->renderHtmlElement() . $this->addElementsFromTemplates($tplOutput),
                $this->getCleanedUpTemplateOutput($tplOutput)
            );
        }

        return $tplOutput;
    }
}
