<?php
namespace ACP3\Core\View\Renderer\Smarty\Filters;
use ACP3\Core\View\Renderer\Smarty\AbstractPlugin;
use ACP3\Core\View\Renderer\Smarty\PluginInterface;

/**
 * Class AbstractFilter
 * @package ACP3\Core\View\Renderer\Smarty\Filters
 */
abstract class AbstractFilter extends AbstractPlugin
{
    /**
     * @inheritdoc
     */
    public function getExtensionType()
    {
        return PluginInterface::EXTENSION_TYPE_FILTER;
    }

    /**
     * @param                           $tpl_output
     * @param \Smarty_Internal_Template $smarty
     *
     * @return string
     */
    abstract public function process($tpl_output, \Smarty_Internal_Template $smarty);
}
