<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\DataGrid\ColumnRenderer;

use ACP3\Core\Helpers\DataGrid\ColumnRenderer\Event\CustomOptionEvent;
use ACP3\Core\Helpers\DataGrid\ColumnRenderer\OptionColumnRenderer\OptionRenderer;
use ACP3\Core\I18n\Translator;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @deprecated Since version 4.30.0, to be removed in 5.0.0. Use class ACP3\Core\DataGrid\ColumnRenderer\OptionColumnRenderer instead
 */
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
     * @var \ACP3\Core\Helpers\DataGrid\ColumnRenderer\OptionColumnRenderer\OptionRenderer
     */
    protected $optionRenderer;

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
            new CustomOptionEvent($this->optionRenderer, $dbResultRow, $this->identifier),
            'data_grid.column_renderer.custom_option_before'
        );

        if ($column['custom']['can_edit']) {
            $resourcePathEdit = $column['custom']['resource_path_edit'];
            $resourcePathEdit .= !\preg_match('=/$=', $resourcePathEdit) ? '/' : '';
            $this->optionRenderer->addOption(
                $resourcePathEdit . 'id_' . $dbResultRow[$this->primaryKey],
                $this->translator->t('system', 'edit'),
                'glyphicon-edit',
                'btn-default'
            );
        }

        $this->eventDispatcher->dispatch(
            new CustomOptionEvent($this->optionRenderer, $dbResultRow, $this->identifier),
            'data_grid.column_renderer.custom_option_between'
        );

        if ($column['custom']['can_delete']) {
            $resourcePathDelete = $column['custom']['resource_path_delete'];
            $resourcePathDelete .= !\preg_match('=/$=', $resourcePathDelete) ? '/' : '';
            $this->optionRenderer->addOption(
                $resourcePathDelete . 'entries_' . $dbResultRow[$this->primaryKey],
                $this->translator->t('system', 'delete'),
                'glyphicon-remove',
                'btn-danger'
            );
        }

        $this->eventDispatcher->dispatch(
            new CustomOptionEvent($this->optionRenderer, $dbResultRow, $this->identifier),
            'data_grid.column_renderer.custom_option_after'
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
}
