<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Filters;

/**
 * Class AbstractMoveElementFilter
 * @package ACP3\Core\View\Renderer\Smarty\Filters
 */
abstract class AbstractMoveElementFilter extends AbstractFilter
{
    const ELEMENT_CATCHER_REGEX_PATTERN = '';
    const PLACEHOLDER = '';

    /**
     * @param string $tplOutput
     * @return string
     */
    protected function getCleanedUpTemplateOutput($tplOutput)
    {
        return preg_replace(static::ELEMENT_CATCHER_REGEX_PATTERN, '', $tplOutput);
    }

    /**
     * @param string $tplOutput
     * @return string
     */
    protected function addElementsFromTemplates($tplOutput)
    {
        $matches = [];
        preg_match_all(static::ELEMENT_CATCHER_REGEX_PATTERN, $tplOutput, $matches);

        return implode("\n", array_unique($matches[1])) . "\n";
    }
}
