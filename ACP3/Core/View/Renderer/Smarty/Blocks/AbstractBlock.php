<?php
namespace ACP3\Core\View\Renderer\Smarty\Blocks;

/**
 * Class AbstractBlock
 * @package ACP3\Core\View\Renderer\Smarty\Block
 */
abstract class AbstractBlock
{
    /**
     * @var string
     */
    protected $blockName = '';

    /**
     * @param \Smarty $smarty
     */
    public function registerBlock(\Smarty $smarty)
    {
        $smarty->registerPlugin('block', $this->blockName, array($this, 'process'));
    }

    /**
     * @param $params
     * @param $content
     * @param \Smarty_Internal_Template $smarty
     * @param $repeat
     * @return string
     */
    abstract public function process($params, $content, \Smarty_Internal_Template $smarty, &$repeat);
} 