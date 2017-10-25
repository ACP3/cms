<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;


use ACP3\Core\I18n\Translator;
use ACP3\Core\Router\RouterInterface;

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
     * @param RouterInterface $router
     * @param Translator $translator
     */
    public function __construct(RouterInterface $router, Translator $translator)
    {
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * @inheritdoc
     */
    protected function getValue(array $column, array $dbResultRow)
    {
        $value = parent::getValue($column, $dbResultRow);

        if (!empty($column['custom']['path'])) {
            $route = $this->router->route(sprintf($column['custom']['path'], $value));
            $value = '<a href="' . $route . '" target="_blank" title="' . $this->translator->t('system', 'open_in_new_window') . '">' . $value . '</a>';
        }

        return $value;
    }
}
