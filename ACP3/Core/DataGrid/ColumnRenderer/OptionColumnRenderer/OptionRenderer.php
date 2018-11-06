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
     *
     * @param \ACP3\Core\Router\RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param string $route
     * @param string $translationPhrase
     * @param string $iconClass
     * @param string $buttonClass
     * @param bool   $useAjax
     */
    public function addOption(
        string $route,
        string $translationPhrase,
        string $iconClass,
        string $buttonClass = 'btn-light',
        bool $useAjax = false
    ) {
        $ajax = $useAjax === true ? ' data-ajax-form="true"' : '';
        $value = ' <a href="' . $this->router->route($route) . '" class="ml-1 btn btn-sm ' . $buttonClass . '"' . $ajax . ' title="' . $translationPhrase . '">';
        $value .= '<i class="fas ' . $iconClass . '"></i>';
        $value .= '<span class="sr-only">' . $translationPhrase . '</span>';
        $value .= '</a>';

        $this->options[] = $value;
    }

    /**
     * @return array
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
