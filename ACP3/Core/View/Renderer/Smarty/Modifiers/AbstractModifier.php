<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Modifiers;

use ACP3\Core\View\Renderer\Smarty\PluginInterface;
use ACP3\Core\View\Renderer\Smarty\PluginTypeEnum;

abstract class AbstractModifier implements PluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getExtensionType()
    {
        return PluginTypeEnum::MODIFIER;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \SmartyException
     */
    public function register(\Smarty $smarty)
    {
        $smarty->registerPlugin(PluginTypeEnum::MODIFIER, $this->getExtensionName(), $this);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    abstract public function __invoke($value): string;
}
