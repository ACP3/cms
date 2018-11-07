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
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $eventDispatcher;
    /**
     * @var \ACP3\Core\DataGrid\ColumnRenderer\OptionColumnRenderer\OptionRenderer
     */
    protected $optionRenderer;

    /**
     * @param \ACP3\Core\I18n\Translator                                             $translator
     * @param \ACP3\Core\DataGrid\ColumnRenderer\OptionColumnRenderer\OptionRenderer $optionRenderer
     * @param \Symfony\Component\EventDispatcher\EventDispatcher                     $eventDispatcher
     */
    public function __construct(
        Translator $translator,
        OptionRenderer $optionRenderer,
        EventDispatcher $eventDispatcher
    ) {
        $this->translator = $translator;
        $this->optionRenderer = $optionRenderer;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        $this->eventDispatcher->dispatch(
            'data_grid.column_renderer.custom_option_before',
            new CustomOptionEvent($this->optionRenderer, $dbResultRow, $this->getIdentifier())
        );

        if ($column['custom']['can_edit']) {
            $resourcePathEdit = $column['custom']['resource_path_edit'];
            $resourcePathEdit .= !\preg_match('=/$=', $resourcePathEdit) ? '/' : '';
            $this->optionRenderer->addOption(
                $this->getEditRoute($dbResultRow, $resourcePathEdit),
                $this->translator->t('system', 'edit'),
                'glyphicon-edit',
                'btn-default'
            );
        }

        $this->eventDispatcher->dispatch(
            'data_grid.column_renderer.custom_option_between',
            new CustomOptionEvent($this->optionRenderer, $dbResultRow, $this->getIdentifier())
        );

        if ($column['custom']['can_delete']) {
            $resourcePathDelete = $column['custom']['resource_path_delete'];
            $resourcePathDelete .= !\preg_match('=/$=', $resourcePathDelete) ? '/' : '';
            $this->optionRenderer->addOption(
                $resourcePathDelete . 'entries_' . $dbResultRow[$this->getPrimaryKey()],
                $this->translator->t('system', 'delete'),
                'glyphicon-remove',
                'btn-danger'
            );
        }

        $this->eventDispatcher->dispatch(
            'data_grid.column_renderer.custom_option_after',
            new CustomOptionEvent($this->optionRenderer, $dbResultRow, $this->getIdentifier())
        );

        return $this->render($column, $this->collectOptions());
    }

    /**
     * @return string
     */
    protected function collectOptions()
    {
        $value = '<div class="datagrid-column__action-buttons">';

        foreach ($this->optionRenderer->getOptions() as $option) {
            $value .= $option;
        }

        $this->optionRenderer->clearOptions();

        $value .= '</div>';

        return $value;
    }

    /**
     * @param array  $dbResultRow
     * @param string $resourcePathEdit
     *
     * @return string
     */
    private function getEditRoute(array $dbResultRow, string $resourcePathEdit): string
    {
        if (\strpos($resourcePathEdit, '%s') === false) {
            return $resourcePathEdit . 'id_' . $dbResultRow[$this->getPrimaryKey()];
        }

        return \sprintf($resourcePathEdit, $dbResultRow[$this->getPrimaryKey()]);
    }
}
