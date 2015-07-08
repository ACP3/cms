<?php
namespace ACP3\Core\View\Renderer\Smarty\Blocks;

/**
 * Class Stylesheets
 * @package ACP3\Core\View\Renderer\Smarty\Blocks
 */
class Stylesheets extends AbstractBlock
{
    /**
     * @inheritdoc
     */
    public function getPluginName()
    {
        return 'stylesheets';
    }

    /**
     * @param                           $params
     * @param                           $content
     * @param \Smarty_Internal_Template $smarty
     * @param                           $repeat
     *
     * @return string
     */
    public function process($params, $content, \Smarty_Internal_Template $smarty, &$repeat)
    {
        if (!$repeat) {
            if (isset($content)) {
                return '@@@SMARTY:STYLESHEETS:BEGIN@@@' . trim($content) . '@@@SMARTY:STYLESHEETS:END@@@';
            }
        }

        return '';
    }
}
