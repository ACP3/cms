<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\View\Renderer\Smarty\AbstractPlugin;
use ACP3\Core\View\Renderer\Smarty\PluginInterface;

/**
 * Class AbstractFunction
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
abstract class AbstractFunction extends AbstractPlugin
{
    /**
     * @return string
     */
    public function getExtensionType()
    {
        return PluginInterface::EXTENSION_TYPE_FUNCTION;
    }

    /**
     * @param array                     $params
     * @param \Smarty_Internal_Template $smarty
     *
     * @return mixed
     */
    abstract public function process(array $params, \Smarty_Internal_Template $smarty);
}
