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
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;
    /**
     * @var array
     */
    private $options = [];
    /**
     * @var Icon
     */
    private $icon;

    public function __construct(RouterInterface $router, Icon $icon)
    {
        $this->router = $router;
        $this->icon = $icon;
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
        $value .= ($this->icon)('solid', strpos($icon, 'fa-') === 0 ? substr($icon, 3) : $icon);
        $value .= '<span class="visually-hidden">' . $translationPhrase . '</span>';
        $value .= '</a>';

        $this->options[] = $value;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function clearOptions(): void
    {
        $this->options = [];
    }
}
