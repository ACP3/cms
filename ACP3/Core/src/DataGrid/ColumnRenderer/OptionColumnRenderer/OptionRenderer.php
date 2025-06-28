<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer\OptionColumnRenderer;

use ACP3\Core\Helpers\View\Icon;
use ACP3\Core\Router\RouterInterface;

class OptionRenderer
{
    /**
     * @var string[]
     */
    private array $mainOptions = [];
    /**
     * @var string[]
     */
    private array $dropDownOptions = [];

    public function __construct(private readonly RouterInterface $router, private readonly Icon $icon)
    {
    }

    public function addOption(
        string $route,
        string $translationPhrase,
        string $icon,
        string $iconSelector = '',
        bool $useAjax = false,
        bool $shownByDefault = false,
    ): void {
        if ($shownByDefault) {
            $ajax = $useAjax === true ? ' data-ajax-form="true"' : '';
            $value = '<a href="' . $this->router->route($route) . '" class="btn btn-sm btn-light" title="' . $translationPhrase . '"' . $ajax . '>';
            $value .= ($this->icon)('solid', str_starts_with($icon, 'fa-') ? substr($icon, 3) : $icon, ['cssSelectors' => $iconSelector]);
            $value .= '</a>';

            $this->mainOptions[] = $value;
        } else {
            $ajax = $useAjax === true ? ' data-ajax-form="true"' : '';
            $value = '<li><a href="' . $this->router->route($route) . '" class="dropdown-item"' . $ajax . '>';
            $value .= ($this->icon)('solid', str_starts_with($icon, 'fa-') ? substr($icon, 3) : $icon, ['cssSelectors' => $iconSelector]);
            $value .= '<span class="ms-2">' . $translationPhrase . '</span>';
            $value .= '</a></li>';

            $this->dropDownOptions[] = $value;
        }
    }

    /**
     * @return string[]
     */
    public function getDropDownOptions(): array
    {
        return $this->dropDownOptions;
    }

    /**
     * @return string[]
     */
    public function getMainOptions(): array
    {
        return $this->mainOptions;
    }

    public function clearOptions(): void
    {
        $this->mainOptions = [];
        $this->dropDownOptions = [];
    }
}
