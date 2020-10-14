<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer\OptionColumnRenderer;

use ACP3\Core\Router\RouterInterface;

class OptionRenderer
{
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    protected $router;
    /**
     * @var array
     */
    protected $options = [];

    /**
     * OptionRenderer constructor.
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function addOption(
        string $route,
        string $translationPhrase,
        string $iconClass,
        string $buttonClass = 'btn-default',
        bool $useAjax = false
    ) {
        $ajax = $useAjax === true ? ' data-ajax-form="true"' : '';
        $value = ' <a href="' . $this->router->route($route) . '" class="btn btn-xs ' . $buttonClass . '"' . $ajax . ' title="' . $translationPhrase . '">';
        $value .= '<i class="fa ' . $iconClass . '"></i>';
        $value .= '<span class="sr-only">' . $translationPhrase . '</span>';
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
