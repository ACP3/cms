<?php
namespace ACP3\Core\View\Renderer\Smarty\Blocks;

/**
 * Class Javascripts
 * @package ACP3\Core\View\Renderer\Smarty\Blocks
 */
class Javascripts extends AbstractBlock
{
    /**
     * @inheritdoc
     */
    public function getPluginName()
    {
        return 'javascripts';
    }

    /**
     * @param $params
     * @param $content
     * @param \Smarty_Internal_Template $smarty
     * @param $repeat
     * @return string
     */
    public function process($params, $content, \Smarty_Internal_Template $smarty, &$repeat)
    {
        if (!$repeat) {
            if (isset($content)) {
                return '@@@SMARTY:JAVASCRIPTS:BEGIN@@@' . trim($content) . '@@@SMARTY:JAVASCRIPTS:END@@@';
            }
        }

        return '';
    }
}