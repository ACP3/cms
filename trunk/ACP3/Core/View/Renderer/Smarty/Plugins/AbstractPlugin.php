<?php
namespace ACP3\Core\View\Renderer\Smarty\Plugins;

/**
 * Class AbstractPlugin
 * @package ACP3\Core\View\Renderer\Smarty\Plugins
 */
abstract class AbstractPlugin
{
    /**
     * @var string
     */
    protected $pluginName = '';

    /**
     * @param \Smarty $smarty
     * @throws \SmartyException
     */
    public function registerPlugin(\Smarty $smarty)
    {
        $smarty->registerPlugin('function', $this->pluginName, array($this, 'process'));
    }

    /**
     * @param array $params
     * @param \Smarty_Internal_Template $smarty
     * @return mixed
     */
    abstract public function process(array $params, \Smarty_Internal_Template $smarty);
} 