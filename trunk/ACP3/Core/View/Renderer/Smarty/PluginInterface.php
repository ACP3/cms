<?php
namespace ACP3\Core\View\Renderer\Smarty;

/**
 * Interface PluginInterface
 * @package ACP3\Core\View\Renderer\Smarty
 */
interface PluginInterface
{
    /**
     * @return string
     */
    public function getPluginType();

    /**
     * @return string
     */
    public function getPluginName();

    /**
     * @param \Smarty $smarty
     */
    public function registerPlugin(\Smarty $smarty);
}