<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Modifiers;

/**
 * Class AddProtocol
 * @package ACP3\Core\View\Renderer\Smarty\Modifiers
 */
class PrefixUri extends AbstractModifier
{
    /**
     * @inheritdoc
     */
    public function getExtensionName()
    {
        return 'prefix_uri';
    }

    /**
     * @inheritdoc
     */
    public function process($value)
    {
        if (!empty($value) && (bool)preg_match('=^http(s)?://=', $value) === false) {
            return 'http://' . $value;
        }

        return '';
    }
}
