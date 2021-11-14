<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;

class ConfigProcessor
{
    public function __construct(private RequestInterface $request, private Translator $translator)
    {
    }

    public function generateDataTableConfig(Input $options): array
    {
        $config = [
            'lengthMenu' => $this->getLengthMenu(),
            'stateSave' => true,
            'autoWidth' => false,
            'language' => $this->getLanguage(),
            'sorting' => $this->getDefaultSorting($options->getColumns()),
            'columns' => $this->getColumnDefinitions($options->getColumns(), $options->isUseAjax()),
        ];

        if ($options->isUseAjax()) {
            $config['ajax'] = $this->request->getFullPath() . 'ajax_' . substr($options->getIdentifier(), 1);
        }

        return [
            'identifier' => $options->getIdentifier(),
            'config' => json_encode($config, JSON_THROW_ON_ERROR),
        ];
    }

    private function getLengthMenu(): array
    {
        return [
            array_keys($this->getLengthMap()),
            array_values($this->getLengthMap()),
        ];
    }

    private function getLengthMap(): array
    {
        return [
            10 => 10,
            15 => 15,
            20 => 20,
            25 => 25,
            50 => 50,
            -1 => $this->translator->t('system', 'data_table_all'),
        ];
    }

    private function getLanguage(): array
    {
        return [
            'loadingRecords' => $this->translator->t('system', 'data_table_loading_records'),
            'emptyTable' => $this->translator->t('system', 'no_entries'),
            'search' => $this->translator->t('system', 'data_table_search'),
            'lengthMenu' => $this->translator->t('system', 'data_table_length_menu'),
            'zeroRecords' => $this->translator->t('system', 'data_table_zero_records'),
            'info' => $this->translator->t('system', 'data_table_info'),
            'infoEmpty' => $this->translator->t('system', 'data_table_info_empty'),
            'infoFiltered' => $this->translator->t('system', 'data_table_info_filtered'),
            'paginate' => [
                'previous' => $this->translator->t('system', 'previous'),
                'next' => $this->translator->t('system', 'next'),
            ],
        ];
    }

    protected function getDefaultSorting(ColumnPriorityQueue $columns): array
    {
        $i = 0;

        foreach (clone $columns as $column) {
            if ($column['default_sort'] === true &&
                \in_array($column['default_sort_direction'], ['asc', 'desc'])
            ) {
                return [
                    [
                        $i,
                        $column['default_sort_direction'],
                    ],
                ];
            }

            if (!empty($column['label'])) {
                ++$i;
            }
        }

        return [];
    }

    private function getColumnDefinitions(ColumnPriorityQueue $columns, bool $useAjax): array
    {
        $columnDefinitions = [];
        $i = 0;

        foreach (clone $columns as $column) {
            if ($column['sortable'] === false) {
                $columnDefinitions[$i]['orderable'] = false;
            }
            if ($useAjax && !empty($column['class'])) {
                $columnDefinitions[$i]['className'] = $column['class'];
            }
            if ($useAjax && \is_callable($column['type'] . '::mandatoryAttributes')) {
                $attributes = ($column['type'] . '::mandatoryAttributes')();
                if (\is_array($attributes) && !empty($attributes)) {
                    $mapper = [];
                    foreach ($attributes as $attribute) {
                        $mapper[$attribute] = $attribute;
                    }
                    $columnDefinitions[$i]['render'] = $mapper;
                }
            }
            if (empty($columnDefinitions[$i])) {
                $columnDefinitions[$i] = null;
            }

            if (!empty($column['label'])) {
                ++$i;
            }
        }

        return $columnDefinitions;
    }
}
