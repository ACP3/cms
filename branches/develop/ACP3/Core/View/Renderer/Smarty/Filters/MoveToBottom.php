<?php
namespace ACP3\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\Assets;
use ACP3\Core\Request;

/**
 * Class MoveToBottom
 * @package ACP3\Core\View\Renderer\Smarty\Filters
 */
class MoveToBottom extends AbstractFilter
{
    /**
     * @var string
     */
    protected $filterType = 'output';

    /**
     * @var Assets
     */
    protected $assets;
    /**
     * @var Request
     */
    protected $request;

    /**
     * @param Assets $assets
     * @param Request $request
     */
    public function __construct(
        Assets $assets,
        Request $request
    ) {
        $this->assets = $assets;
        $this->request = $request;
    }

    /**
     * @param $tpl_output
     * @param \Smarty_Internal_Template $smarty
     * @return string
     */
    public function process($tpl_output, \Smarty_Internal_Template $smarty)
    {
        $pattern = '!@@@SMARTY:JAVASCRIPTS:BEGIN@@@(.*?)@@@SMARTY:JAVASCRIPTS:END@@@!is';

        if (strpos($tpl_output, '<!-- JAVASCRIPTS -->') !== false) {
            $matches = [];
            preg_match_all($pattern, $tpl_output, $matches);

            // Remove placeholder comments
            $tpl_output = preg_replace($pattern, '', $tpl_output);

            $minifyJs = '';
            if (!$this->request->getIsAjax()) {
                $minifyJs = '<script type="text/javascript" src="' . $this->assets->buildMinifyLink('js') . '"></script>' . "\n";
            }

            return str_replace('<!-- JAVASCRIPTS -->', $minifyJs . implode("\n", array_unique($matches[1])) . "\n", $tpl_output);
        }

        return $tpl_output;
    }
}
