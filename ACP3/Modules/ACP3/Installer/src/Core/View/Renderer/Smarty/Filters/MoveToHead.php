<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\Assets\EventListener\StaticAssetsListener;
use ACP3\Core\Assets\Renderer\CSSRenderer;
use ACP3\Core\Assets\Renderer\JavaScriptRenderer;
use ACP3\Core\View\Renderer\Smarty\Filters\AbstractFilter;

class MoveToHead extends AbstractFilter
{
    public function __construct(private readonly CSSRenderer $CSSRenderer, private readonly JavaScriptRenderer $jsRenderer)
    {
    }

    public function __invoke(string $tplOutput, \Smarty_Internal_Template $smarty): string
    {
        if (str_contains($tplOutput, StaticAssetsListener::PLACEHOLDER_CSS)) {
            $assets = $this->CSSRenderer->renderHtmlElement();
            $assets .= $this->jsRenderer->renderHtmlElement();
            $assets .= $this->addElementsFromTemplates(StaticAssetsListener::REGEX_PATTERN_CSS, $tplOutput);
            $assets .= $this->addElementsFromTemplates(StaticAssetsListener::REGEX_PATTERN_JS, $tplOutput);

            $tplOutput = str_replace(
                StaticAssetsListener::PLACEHOLDER_CSS,
                $assets,
                $this->getCleanedUpTemplateOutput(
                    StaticAssetsListener::REGEX_PATTERN_JS,
                    $this->getCleanedUpTemplateOutput(StaticAssetsListener::REGEX_PATTERN_CSS, $tplOutput)
                )
            );
        }

        return $tplOutput;
    }

    private function getCleanedUpTemplateOutput(string $pattern, string $tplOutput): string
    {
        return preg_replace($pattern, '', $tplOutput);
    }

    private function addElementsFromTemplates(string $pattern, string $tplOutput): string
    {
        $matches = [];
        preg_match_all($pattern, $tplOutput, $matches);

        return implode("\n", array_unique($matches[1])) . "\n";
    }
}
