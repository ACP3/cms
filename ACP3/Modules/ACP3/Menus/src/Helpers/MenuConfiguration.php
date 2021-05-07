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
    private $useBootstrap;
    /**
     * @var string
     */
    private $selector;
    /**
     * @var string
     */
    private $dropdownItemSelector;
    /**
     * @var string
     */
    private $tag;
    /**
     * @var string
     */
    private $itemTag;
    /**
     * @var string
     */
    private $dropdownWrapperTag;
    /**
     * @var string
     */
    private $linkSelector;
    /**
     * @var string
     */
    private $inlineStyle;

    public function __construct(
        bool $useBootstrap = true,
        string $class = '',
        string $dropdownItemSelector = '',
        string $tag = 'ul',
        string $itemTag = 'li',
        string $dropdownWrapperTag = 'li',
        string $linkSelectors = '',
        string $inlineStyle = ''
    ) {
        $this->useBootstrap = $useBootstrap;
        $this->selector = $class;
        $this->dropdownItemSelector = $dropdownItemSelector;
        $this->tag = $tag;
        $this->itemTag = $itemTag;
        $this->dropdownWrapperTag = $dropdownWrapperTag;
        $this->linkSelector = $linkSelectors;
        $this->inlineStyle = $inlineStyle;
    }

    public function isUseBootstrap(): bool
    {
        return $this->useBootstrap;
    }

    public function getSelector(): string
    {
        return $this->selector;
    }

    public function getDropdownItemSelector(): string
    {
        return $this->dropdownItemSelector;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getItemTag(): string
    {
        return $this->itemTag;
    }

    public function getDropdownWrapperTag(): string
    {
        return $this->dropdownWrapperTag;
    }

    public function getLinkSelector(): string
    {
        return $this->linkSelector;
    }

    public function getInlineStyle(): string
    {
        return $this->inlineStyle;
    }

    public function __toString(): string
    {
        return implode(':', get_object_vars($this));
    }
}
