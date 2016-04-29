<?php
namespace ACP3\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\Assets\MinifierInterface;

/**
 * Class MoveToHead
 * @package ACP3\Core\View\Renderer\Smarty\Filters
 */
class MoveToHead extends AbstractMoveElementFilter
{
    const ELEMENT_CATCHER_REGEX_PATTERN = "!@@@SMARTY:STYLESHEETS:BEGIN@@@(.*?)@@@SMARTY:STYLESHEETS:END@@@!is";
    const PLACEHOLDER = '<!-- STYLESHEETS -->';

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
        if (strpos($tplOutput, static::PLACEHOLDER) !== false) {
            $tplOutput = str_replace(
                static::PLACEHOLDER,
                $this->addElementFromMinifier() . $this->addElementsFromTemplates($tplOutput),
                $this->getCleanedUpTemplateOutput($tplOutput)
            );
        }

        return $tplOutput;
    }

    /**
     * @return string
     */
    protected function addElementFromMinifier()
    {
        return '<link rel="stylesheet" type="text/css" href="' . $this->minifier->getURI() . '">' . "\n";
    }
}
