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
    private array $options = [];

    public function __construct(private RouterInterface $router, private Icon $icon)
    {
    }

    public function addOption(
        string $route,
        string $translationPhrase,
        string $icon,
        string $buttonClass = 'btn-outline-secondary',
        bool $useAjax = false
    ): void {
        $ajax = $useAjax === true ? ' data-ajax-form="true"' : '';
        $value = ' <a href="' . $this->router->route($route) . '" class="btn btn-sm ' . $buttonClass . '"' . $ajax . ' title="' . $translationPhrase . '">';
        $value .= ($this->icon)('solid', str_starts_with($icon, 'fa-') ? substr($icon, 3) : $icon);
        $value .= '<span class="visually-hidden">' . $translationPhrase . '</span>';
        $value .= '</a>';

        $this->options[] = $value;
    }

    /**
     * @return string[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function clearOptions(): void
    {
        $this->options = [];
    }
}
