<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\View\Renderer\Smarty\Filters\AbstractMoveElementFilter;

class MoveToHead extends AbstractMoveElementFilter
{
    const ELEMENT_CATCHER_REGEX_PATTERN = '!@@@SMARTY:STYLESHEETS:BEGIN@@@(.*?)@@@SMARTY:STYLESHEETS:END@@@!is';
    const PLACEHOLDER = '<!-- STYLESHEETS -->';

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
    public function process($tplOutput, \Smarty_Internal_Template $smarty)
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

    /**
     * {@inheritdoc}
     */
    public function getExtensionName()
    {
        return 'output';
    }
}
