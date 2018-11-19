<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\View\Renderer\Smarty\Filters\AbstractMoveElementFilter;

class MoveToBottom extends AbstractMoveElementFilter
{
    const ELEMENT_CATCHER_REGEX_PATTERN = '!@@@SMARTY:JAVASCRIPTS:BEGIN@@@(.*?)@@@SMARTY:JAVASCRIPTS:END@@@!is';
    const PLACEHOLDER = '<!-- JAVASCRIPTS -->';

    /**
     * {@inheritdoc}
     */
    public function __invoke($tplOutput, \Smarty_Internal_Template $smarty)
    {
        if (\strpos($tplOutput, static::PLACEHOLDER) !== false) {
            return \str_replace(
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
    protected function addElementFromMinifier()
    {
        return '';
    }
}
