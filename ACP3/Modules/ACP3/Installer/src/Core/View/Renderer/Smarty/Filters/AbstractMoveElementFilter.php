<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\View\Renderer\Smarty\Filters\AbstractFilter;

abstract class AbstractMoveElementFilter extends AbstractFilter
{
    public const ELEMENT_CATCHER_REGEX_PATTERN = '';
    protected const PLACEHOLDER = '';

    protected function getCleanedUpTemplateOutput(string $tplOutput): string
    {
        return \preg_replace(static::ELEMENT_CATCHER_REGEX_PATTERN, '', $tplOutput);
    }

    protected function addElementsFromTemplates(string $tplOutput): string
    {
        $matches = [];
        \preg_match_all(static::ELEMENT_CATCHER_REGEX_PATTERN, $tplOutput, $matches);

        return \implode("\n", \array_unique($matches[1])) . "\n";
    }
}
