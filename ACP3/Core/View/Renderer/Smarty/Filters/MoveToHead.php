<?php
namespace ACP3\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\Assets\MinifierInterface;

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
     * @var \ACP3\Core\Assets\MinifierInterface
     */
    protected $minifier;

    /**
     * @param \ACP3\Core\Assets\MinifierInterface $minifier
     */
    public function __construct(MinifierInterface $minifier)
    {
        $this->minifier = $minifier;
    }

    /**
     * @param                           $tpl_output
     * @param \Smarty_Internal_Template $smarty
     *
     * @return string
     */
    public function process($tpl_output, \Smarty_Internal_Template $smarty)
    {
        if (strpos($tpl_output, '<!-- STYLESHEETS -->') !== false) {
            $matches = [];
            preg_match_all('!@@@SMARTY:STYLESHEETS:BEGIN@@@(.*?)@@@SMARTY:STYLESHEETS:END@@@!is', $tpl_output, $matches);

            // Remove placeholder comments
            $tpl_output = preg_replace("!@@@SMARTY:STYLESHEETS:BEGIN@@@(.*?)@@@SMARTY:STYLESHEETS:END@@@!is", '', $tpl_output);
            $minifyCss = '<link rel="stylesheet" type="text/css" href="' . $this->minifier->getURI() . '">' . "\n";
            return str_replace('<!-- STYLESHEETS -->', $minifyCss . implode("\n", array_unique($matches[1])) . "\n", $tpl_output);
        }

        return $tpl_output;
    }
}
