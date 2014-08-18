<?php
namespace ACP3\Core\View\Renderer\Smarty;

/**
 * Class AbstractPlugin
 * @package ACP3\Core\View\Renderer\Smarty
 */
abstract class AbstractPlugin
{
    /**
     * @var string
     */
    protected $pluginName = '';

    /**
     * @throws \SmartyException
     */
    public function registerPlugin(\Smarty $smarty)
    {
        $smarty->registerPlugin('function', $this->pluginName, array($this, 'process'));
    }

    /**
     * @param $params
     *
     * @return mixed
     */
    abstract public function process($params);
} 