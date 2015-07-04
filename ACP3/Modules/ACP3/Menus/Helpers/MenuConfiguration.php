<?php
namespace ACP3\Modules\ACP3\Menus\Helpers;

/**
 * Class MenuConfiguration
 * @package ACP3\Modules\ACP3\Menus\Helpers
 */
class MenuConfiguration
{
    /**
     * @var bool
     */
    protected $useBootstrap = true;
    /**
     * @var string
     */
    protected $selector = '';
    /**
     * @var string
     */
    protected $dropdownItemSelector = '';
    /**
     * @var string
     */
    protected $tag = 'ul';
    /**
     * @var string
     */
    protected $itemTag = 'li';
    /**
     * @var string
     */
    protected $dropdownWrapperTag = 'li';
    /**
     * @var string
     */
    protected $linkSelector = '';
    /**
     * @var string
     */
    protected $inlineStyle = '';

    /**
     * @param bool   $useBootstrap
     * @param string $class
     * @param string $dropdownItemSelector
     * @param string $tag
     * @param string $itemTag
     * @param string $dropdownWrapperTag
     * @param string $linkSelectors
     * @param string $inlineStyle
     */
    public function __construct(
        $useBootstrap = true,
        $class = '',
        $dropdownItemSelector = '',
        $tag = 'ul',
        $itemTag = 'li',
        $dropdownWrapperTag = 'li',
        $linkSelectors = '',
        $inlineStyle = ''
    )
    {
        $this->useBootstrap = $useBootstrap;
        $this->selector = $class;
        $this->dropdownItemSelector = $dropdownItemSelector;
        $this->tag = $tag;
        $this->itemTag = $itemTag;
        $this->dropdownWrapperTag = $dropdownWrapperTag;
        $this->linkSelector = $linkSelectors;
        $this->inlineStyle = $inlineStyle;
    }

    /**
     * @return boolean
     */
    public function isUseBootstrap()
    {
        return $this->useBootstrap;
    }

    /**
     * @return string
     */
    public function getSelector()
    {
        return $this->selector;
    }

    /**
     * @return string
     */
    public function getDropdownItemSelector()
    {
        return $this->dropdownItemSelector;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @return string
     */
    public function getItemTag()
    {
        return $this->itemTag;
    }

    /**
     * @return string
     */
    public function getDropdownWrapperTag()
    {
        return $this->dropdownWrapperTag;
    }

    /**
     * @return string
     */
    public function getLinkSelector()
    {
        return $this->linkSelector;
    }

    /**
     * @return string
     */
    public function getInlineStyle()
    {
        return $this->inlineStyle;
    }

}