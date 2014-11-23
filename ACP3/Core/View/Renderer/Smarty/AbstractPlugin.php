<?php
namespace ACP3\Core\View\Renderer\Smarty;

/**
 * Class AbstractPlugin
 * @package ACP3\Core\View\Renderer\Smarty
 */
abstract class AbstractPlugin implements PluginInterface
{
    /**
     * @param \Smarty $smarty
     * @throws \SmartyException
     */
    public function registerPlugin(\Smarty $smarty)
    {
        $smarty->registerPlugin($this->getPluginType(), $this->getPluginName(), [$this, 'process']);
    }
} 