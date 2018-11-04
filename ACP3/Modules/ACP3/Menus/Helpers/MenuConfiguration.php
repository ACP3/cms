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
    private $useBootstrap = true;
    /**
     * @var string[]
     */
    private $selectors;
    /**
     * @var string[]
     */
    private $dropdownItemSelector;
    /**
     * @var string
     */
    private $tag = 'ul';
    /**
     * @var string
     */
    private $itemTag = 'li';
    /**
     * @var string
     */
    private $dropdownWrapperTag = 'li';
    /**
     * @var string[]
     */
    private $linkSelectors;
    /**
     * @var string
     */
    private $inlineStyle = '';
    /**
     * @var string[]
     */
    private $itemSelectors;
    /**
     * @var string[]
     */
    private $subMenuSelectors;

    /**
     * @param bool     $useBootstrap
     * @param string[] $selectors
     * @param string[] $dropdownItemSelector
     * @param string   $tag
     * @param string   $itemTag
     * @param string   $dropdownWrapperTag
     * @param string[] $linkSelectors
     * @param string   $inlineStyle
     * @param string[] $itemSelectors
     * @param string[] $subMenuSelectors
     */
    public function __construct(
        bool $useBootstrap = true,
        array $selectors = [],
        array $dropdownItemSelector = [],
        string $tag = 'ul',
        string $itemTag = 'li',
        string $dropdownWrapperTag = 'li',
        array $linkSelectors = [],
        string $inlineStyle = '',
        array $itemSelectors = [],
        array $subMenuSelectors = []
    ) {
        $this->useBootstrap = $useBootstrap;
        $this->selectors = $selectors;
        $this->dropdownItemSelector = $dropdownItemSelector;
        $this->tag = $tag;
        $this->itemTag = $itemTag;
        $this->dropdownWrapperTag = $dropdownWrapperTag;
        $this->linkSelectors = $linkSelectors;
        $this->inlineStyle = $inlineStyle;
        $this->itemSelectors = $itemSelectors;
        $this->subMenuSelectors = $subMenuSelectors;

        if ($this->useBootstrap) {
            $this->selectors[] = 'nav navbar-nav';
            $this->dropdownItemSelector[] = 'dropdown';
            $this->linkSelectors[] = 'nav-link';
            $this->itemSelectors[] = 'nav-item';
            $this->subMenuSelectors[] = 'dropdown-menu';
        }
    }

    /**
     * @return bool
     */
    public function isUseBootstrap()
    {
        return $this->useBootstrap;
    }

    /**
     * @return string[]
     */
    public function getSelectors(): array
    {
        return $this->selectors;
    }

    /**
     * @return string[]
     */
    public function getDropdownItemSelector(): array
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
     * @return string[]
     */
    public function getLinkSelectors(): array
    {
        return $this->linkSelectors;
    }

    /**
     * @return string
     */
    public function getInlineStyle()
    {
        return $this->inlineStyle;
    }

    /**
     * @return string[]
     */
    public function getItemSelectors(): array
    {
        return $this->itemSelectors;
    }

    /**
     * @return string[]
     */
    public function getSubMenuSelectors(): array
    {
        return $this->subMenuSelectors;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return \serialize(\get_object_vars($this));
    }
}
