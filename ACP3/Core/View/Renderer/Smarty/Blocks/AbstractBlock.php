<?php
namespace ACP3\Core\View\Renderer\Smarty\Blocks;

use ACP3\Core\View\Renderer\Smarty\AbstractPlugin;
use ACP3\Core\View\Renderer\Smarty\PluginInterface;

/**
 * Class AbstractBlock
 * @package ACP3\Core\View\Renderer\Smarty\Blocks
 */
abstract class AbstractBlock extends AbstractPlugin
{
    /**
     * @inheritdoc
     */
    public function getExtensionType()
    {
        return PluginInterface::EXTENSION_TYPE_BLOCK;
    }

    /**
     * @param                           $params
     * @param                           $content
     * @param \Smarty_Internal_Template $smarty
     * @param                           $repeat
     *
     * @return string
     */
    abstract public function process($params, $content, \Smarty_Internal_Template $smarty, &$repeat);
}
