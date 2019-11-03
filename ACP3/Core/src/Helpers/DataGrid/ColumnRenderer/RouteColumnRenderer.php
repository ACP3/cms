<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

use ACP3\Core\I18n\Translator;
use ACP3\Core\Router\RouterInterface;

/**
 * @deprecated Since version 4.30.0, to be removed in 5.0.0. Use class ACP3\Core\DataGrid\ColumnRenderer\RouteColumnRenderer instead
 */
class RouteColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var Translator
     */
    private $translator;

    /**
     * RouteColumnRenderer constructor.
     */
    public function __construct(RouterInterface $router, Translator $translator)
    {
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    protected function getValue(array $column, array $dbResultRow)
    {
        $value = parent::getValue($column, $dbResultRow);

        if (!empty($column['custom']['path'])) {
            $route = $this->router->route(\sprintf($column['custom']['path'], $value));
            $pattern = <<<HTML
<a href="%s" target="_blank" title="%s">%s <small><i class="glyphicon glyphicon-link"></i></small></a>
HTML;
            $value = \sprintf(
                $pattern,
                $route,
                $this->translator->t('system', 'open_in_new_window'),
                $value
            );
        }

        return $value;
    }
}
