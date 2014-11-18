<?php
namespace ACP3\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\Assets;

/**
 * Class MoveToHead
 * @package ACP3\Core\View\Renderer\Smarty\Filters
 */
class MoveToHead extends AbstractFilter
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
        if (strpos($tpl_output, '<!-- STYLESHEETS -->') !== false) {
            $matches = array();
            preg_match_all('!@@@SMARTY:STYLESHEETS:BEGIN@@@(.*?)@@@SMARTY:STYLESHEETS:END@@@!is', $tpl_output, $matches);

            // Remove placeholder comments
            $tpl_output = preg_replace("!@@@SMARTY:STYLESHEETS:BEGIN@@@(.*?)@@@SMARTY:STYLESHEETS:END@@@!is", '', $tpl_output);
            $minifyJs = '<link rel="stylesheet" type="text/css" href="' . $this->assets->buildMinifyLink('css') . '">' . "\n";
            return str_replace('<!-- STYLESHEETS -->', $minifyJs . implode("\n", array_unique($matches[1])) . "\n", $tpl_output);
        }

        return $tpl_output;
    }
}