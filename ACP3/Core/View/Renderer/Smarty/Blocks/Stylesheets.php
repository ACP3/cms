<?php
namespace ACP3\Core\View\Renderer\Smarty\Blocks;

use ACP3\Core\Request;

/**
 * Class Stylesheets
 * @package ACP3\Core\View\Renderer\Smarty\Blocks
 */
class Stylesheets extends AbstractBlock
{
    /**
     * @var string
     */
    protected $blockName = 'stylesheets';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
            if (isset($content) && $this->request->getIsAjax() === false) {
                return '@@@SMARTY:STYLESHEETS:BEGIN@@@' . trim($content) . '@@@SMARTY:STYLESHEETS:END@@@';
            }
        }

        return '';
    }
}