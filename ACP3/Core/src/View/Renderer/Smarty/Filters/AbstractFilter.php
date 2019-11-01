<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\View\Renderer\Smarty\PluginInterface;
use ACP3\Core\View\Renderer\Smarty\PluginTypeEnum;

abstract class AbstractFilter implements PluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getExtensionType()
    {
        return PluginTypeEnum::FILTER;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \SmartyException
     */
    public function register(\Smarty $smarty)
    {
        $smarty->registerFilter($this->getExtensionName(), $this, \get_class($this));
    }

    public function __invoke($tplOutput, \Smarty_Internal_Template $smarty)
    {
        return $this->process($tplOutput, $smarty);
    }

    /**
     * @param string                    $tplOutput
     * @param \Smarty_Internal_Template $smarty
     *
     * @return string
     *
     * @deprecated since version 4.30.0, to be remove with 5.0.0. Implement method __invoke() instead
     */
    abstract public function process($tplOutput, \Smarty_Internal_Template $smarty);
}
