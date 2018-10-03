<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\View\Renderer\Smarty\PluginInterface;
use ACP3\Core\View\Renderer\Smarty\PluginTypeEnum;

abstract class AbstractFunction implements PluginInterface
{
    /**
     * @return string
     */
    public function getExtensionType()
    {
        return PluginTypeEnum::FUNCTION;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \SmartyException
     */
    public function register(\Smarty $smarty)
    {
        $smarty->registerPlugin(PluginTypeEnum::FUNCTION, $this->getExtensionName(), $this);
    }

    /**
     * @param array                     $params
     * @param \Smarty_Internal_Template $smarty
     *
     * @return mixed
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        return $this->process($params, $smarty);
    }

    /**
     * @param array                     $params
     * @param \Smarty_Internal_Template $smarty
     *
     * @return mixed
     *
     * @deprecated since version 4.30.0, to be remove with 5.0.0. Implement method __invoke() instead
     */
    abstract public function process(array $params, \Smarty_Internal_Template $smarty);
}
