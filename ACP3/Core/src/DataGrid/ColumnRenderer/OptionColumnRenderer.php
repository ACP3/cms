<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

use ACP3\Core\DataGrid\ColumnRenderer\Event\CustomOptionEvent;
use ACP3\Core\DataGrid\ColumnRenderer\OptionColumnRenderer\OptionRenderer;
use ACP3\Core\I18n\Translator;
use Symfony\Component\EventDispatcher\EventDispatcher;

class OptionColumnRenderer extends AbstractColumnRenderer
{
    public function __construct(protected Translator $translator, protected OptionRenderer $optionRenderer, protected EventDispatcher $eventDispatcher)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow): string|array
    {
        $this->eventDispatcher->dispatch(
            new CustomOptionEvent($this->optionRenderer, $dbResultRow, $this->getIdentifier()),
            'data_grid.column_renderer.custom_option_before'
        );

        if ($column['custom']['can_edit']) {
            $resourcePathEdit = $column['custom']['resource_path_edit'];
            $resourcePathEdit .= !preg_match('=/$=', $resourcePathEdit) ? '/' : '';
            $this->optionRenderer->addOption(
                $this->getEditRoute($dbResultRow, $resourcePathEdit),
                $this->translator->t('system', 'edit'),
                'pen',
            );
        }

        $this->eventDispatcher->dispatch(
            new CustomOptionEvent($this->optionRenderer, $dbResultRow, $this->getIdentifier()),
            'data_grid.column_renderer.custom_option_between'
        );

        if ($column['custom']['can_delete']) {
            $resourcePathDelete = $column['custom']['resource_path_delete'];
            $resourcePathDelete .= !preg_match('=/$=', $resourcePathDelete) ? '/' : '';
            $this->optionRenderer->addOption(
                $resourcePathDelete . 'entries_' . $dbResultRow[$this->getPrimaryKey()],
                $this->translator->t('system', 'delete'),
                'trash',
                'btn-danger'
            );
        }

        $this->eventDispatcher->dispatch(
            new CustomOptionEvent($this->optionRenderer, $dbResultRow, $this->getIdentifier()),
            'data_grid.column_renderer.custom_option_after'
        );

        return $this->render($column, $this->collectOptions());
    }

    protected function collectOptions(): string
    {
        $value = '<div class="datagrid-column__action-buttons">' . implode('', $this->optionRenderer->getOptions());

        $this->optionRenderer->clearOptions();

        return $value . '</div>';
    }

    /**
     * @param array<string, mixed> $dbResultRow
     */
    private function getEditRoute(array $dbResultRow, string $resourcePathEdit): string
    {
        if (!str_contains($resourcePathEdit, '%s')) {
            return $resourcePathEdit . 'id_' . $dbResultRow[$this->getPrimaryKey()];
        }

        return sprintf($resourcePathEdit, $dbResultRow[$this->getPrimaryKey()]);
    }
}
