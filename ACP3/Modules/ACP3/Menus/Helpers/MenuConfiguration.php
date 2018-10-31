<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Helpers;

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
     * @var string
     */
    private $itemSelectors;

    /**
     * @param bool   $useBootstrap
     * @param string $class
     * @param string $dropdownItemSelector
     * @param string $tag
     * @param string $itemTag
     * @param string $dropdownWrapperTag
     * @param string $linkSelectors
     * @param string $inlineStyle
     * @param string $itemSelectors
     */
    public function __construct(
        bool $useBootstrap = true,
        string $class = '',
        string $dropdownItemSelector = '',
        string $tag = 'ul',
        string $itemTag = 'li',
        string $dropdownWrapperTag = 'li',
        string $linkSelectors = '',
        string $inlineStyle = '',
        string $itemSelectors = ''
    ) {
        $this->useBootstrap = $useBootstrap;
        $this->selector = $class;
        $this->dropdownItemSelector = $dropdownItemSelector;
        $this->tag = $tag;
        $this->itemTag = $itemTag;
        $this->dropdownWrapperTag = $dropdownWrapperTag;
        $this->linkSelector = $linkSelectors;
        $this->inlineStyle = $inlineStyle;
        $this->itemSelectors = $itemSelectors;
    }

    /**
     * @return bool
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

    /**
     * @return string
     */
    public function getItemSelectors(): string
    {
        return $this->itemSelectors;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return \implode(':', \get_object_vars($this));
    }
}
