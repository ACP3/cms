<?php
namespace ACP3\Core\View\Renderer\Smarty;

/**
 * Interface PluginInterface
 * @package ACP3\Core\View\Renderer\Smarty
 */
interface PluginInterface
{
    const EXTENSION_TYPE_BLOCK = 'block';
    const EXTENSION_TYPE_FILTER = 'filter';
    const EXTENSION_TYPE_FUNCTION = 'function';
    const EXTENSION_TYPE_MODIFIER = 'modifier';
    const EXTENSION_TYPE_RESOURCE = 'resource';

    /**
     * @return string
     */
    public function getExtensionType();

    /**
     * @return string
     */
    public function getExtensionName();

    /**
     * @param \Smarty $smarty
     */
    public function register(\Smarty $smarty);
}
