<?php
namespace ACP3\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\Assets;

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
     * @param Assets $assets
     */
    public function __construct(Assets $assets)
    {
        $this->assets = $assets;
    }

    /**
     * @param $tpl_output
     * @param \Smarty_Internal_Template $smarty
     * @return string
     */
    public function process($tpl_output, \Smarty_Internal_Template $smarty)
    {
        if (strpos($tpl_output, '</body>') !== false) {
            $matches = array();
            preg_match_all('!@@@SMARTY:JAVASCRIPTS:BEGIN@@@(.*?)@@@SMARTY:JAVASCRIPTS:END@@@!is', $tpl_output, $matches);

            // Remove placeholder comments
            $tpl_output = preg_replace("!@@@SMARTY:JAVASCRIPTS:BEGIN@@@(.*?)@@@SMARTY:JAVASCRIPTS:END@@@!is", '', $tpl_output);
            $minifyJs = '<script type="text/javascript" src="' . $this->assets->buildMinifyLink('js') . '"></script>' . "\n";
            return str_replace('</body>', $minifyJs . implode("\n", array_unique($matches[1])) . "\n" . '</body>', $tpl_output);
        }

        return $tpl_output;
    }
}