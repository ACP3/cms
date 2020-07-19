<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\View\Renderer\Smarty\Filters\AbstractMoveElementFilter;

class MoveToHead extends AbstractMoveElementFilter
{
    public const ELEMENT_CATCHER_REGEX_PATTERN = '!@@@SMARTY:STYLESHEETS:BEGIN@@@(.*?)@@@SMARTY:STYLESHEETS:END@@@!is';
    protected const PLACEHOLDER = '<!-- STYLESHEETS -->';

    /**
     * {@inheritdoc}
     */
    protected function addElementFromMinifier()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(string $tplOutput, \Smarty_Internal_Template $smarty)
    {
        if (\strpos($tplOutput, static::PLACEHOLDER) !== false) {
            $tplOutput = \str_replace(
                static::PLACEHOLDER,
                $this->addElementFromMinifier() . $this->addElementsFromTemplates($tplOutput),
                $this->getCleanedUpTemplateOutput($tplOutput)
            );
        }

        return $tplOutput;
    }
}
