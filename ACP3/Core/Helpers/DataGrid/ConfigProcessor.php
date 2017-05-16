<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Helpers\DataGrid;


use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\TranslatorInterface;

class ConfigProcessor
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * DataGridConfigProcessor constructor.
     * @param RequestInterface $request
     * @param TranslatorInterface $translator
     */
    public function __construct(RequestInterface $request, TranslatorInterface $translator)
    {
        $this->request = $request;
        $this->translator = $translator;
    }

    /**
     * @param ColumnPriorityQueue $columns
     * @param Options $options
     * @return array
     */
    public function generateDataTableConfig(ColumnPriorityQueue $columns, Options $options): array
    {
        $config = [
            'lengthMenu' => $this->getLengthMenu(),
            'saveState' => true,
            'autoWidth' => false,
            'language' => $this->getLanguage(),
            'sorting' => $this->getDefaultSorting($columns),
            'columns' => $this->getColumnDefinitions($columns, $options->isUseAjax())
        ];

        if ($options->isUseAjax()) {
            $config['ajax'] = $this->request->getFullPath() . 'ajax_' . substr($options->getIdentifier(), 1);
        }

        return [
            'identifier' => $options->getIdentifier(),
            'config' => json_encode($config),
        ];
    }

    /**
     * @return array
     */
    private function getLengthMenu(): array
    {
        return [
            [10, 15, 20, 25, 50, -1],
            [10, 15, 20, 25, 50, $this->translator->t('system', 'data_table_all')]
        ];
    }

    /**
     * @return array
     */
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
            ]
        ];
    }

    /**
     * @param ColumnPriorityQueue $columns
     * @return array
     */
    protected function getDefaultSorting(ColumnPriorityQueue $columns): array
    {
        $i = 0;

        foreach (clone $columns as $column) {
            if ($column['default_sort'] === true &&
                in_array($column['default_sort_direction'], ['asc', 'desc'])
            ) {
                return [
                    [
                        $i,
                        $column['default_sort_direction']
                    ]
                ];
            }

            if (!empty($column['label'])) {
                ++$i;
            }
        }

        return [];
    }

    /**
     * @param ColumnPriorityQueue $columns
     * @param bool $useAjax
     * @return array
     */
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
            if ($useAjax && is_callable($column['type'] . '::mandatoryAttributes')) {
                $attributes = call_user_func($column['type'] . '::mandatoryAttributes');
                if (is_array($attributes) && !empty($attributes)) {
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
