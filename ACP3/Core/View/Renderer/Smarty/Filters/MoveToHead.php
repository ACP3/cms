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
     * @inheritdoc
     */
    public function getExtensionName()
    {
        return 'output';
    }

    /**
     * @inheritdoc
     */
    public function process($tplOutput, \Smarty_Internal_Template $smarty)
    {
        if (strpos($tplOutput, '<!-- STYLESHEETS -->') !== false) {
            $matches = [];
            preg_match_all('!@@@SMARTY:STYLESHEETS:BEGIN@@@(.*?)@@@SMARTY:STYLESHEETS:END@@@!is', $tplOutput, $matches);

            // Remove placeholder comments
            $tplOutput = preg_replace("!@@@SMARTY:STYLESHEETS:BEGIN@@@(.*?)@@@SMARTY:STYLESHEETS:END@@@!is", '', $tplOutput);
            $minifyCss = '<link rel="stylesheet" type="text/css" href="' . $this->minifier->getURI() . '">' . "\n";
            return str_replace('<!-- STYLESHEETS -->', $minifyCss . implode("\n", array_unique($matches[1])) . "\n", $tplOutput);
        }

        return $tplOutput;
    }
}
