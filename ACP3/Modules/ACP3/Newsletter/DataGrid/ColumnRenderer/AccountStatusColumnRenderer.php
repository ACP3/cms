<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\DataGrid\ColumnRenderer;

use ACP3\Core\DataGrid\ColumnRenderer\AbstractColumnRenderer;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Router\RouterInterface;

class AccountStatusColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    protected $router;

    /**
     * AccountStatusColumnRenderer constructor.
     */
    public function __construct(
        Translator $translator,
        RouterInterface $router
    ) {
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        $dbValue = $this->getValue($column, $dbResultRow);

        $column['attribute'] += [
            'sort' => $dbValue,
        ];

        return $this->render($column, $this->getHtmlValue($column, $dbResultRow, $dbValue));
    }

    public static function mandatoryAttributes(): array
    {
        return ['sort', '_'];
    }

    private function getHtmlValue(array $column, array $dbResultRow, ?string $dbValue): string
    {
        if ($dbValue === $this->getDefaultValue($column)) {
            return $this->getDefaultValue($column);
        } elseif ((int) $dbValue === 0 && isset($dbResultRow[$this->getPrimaryKey()])) {
            return \sprintf(
                '<a href="%s" title="%s"><i class="glyphicon glyphicon-remove text-danger"></i></a>',
                $this->router->route('acp/newsletter/accounts/activate/id_' . $dbResultRow[$this->getPrimaryKey()]),
                $this->translator->t('newsletter', 'activate_account')
            );
        }

        return '<i class="glyphicon glyphicon-ok text-success"></i>';
    }
}
