<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid;

use ACP3\Core\ACL;
use ACP3\Core\DataGrid\ColumnRenderer\ColumnRendererInterface;
use ACP3\Core\DataGrid\ColumnRenderer\HeaderColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\MassActionColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\OptionColumnRenderer;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataGrid
{
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\DataGrid\ConfigProcessor
     */
    private $configProcessor;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * @param \ACP3\Core\DataGrid\ConfigProcessor $configProcessor
     */
    public function __construct(
        ContainerInterface $container,
        RequestInterface $request,
        ConfigProcessor $configProcessor,
        ACL $acl,
        Translator $translator
    ) {
        $this->acl = $acl;
        $this->translator = $translator;
        $this->configProcessor = $configProcessor;
        $this->request = $request;
        $this->container = $container;
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     *
     * @return array|JsonResponse
     */
    public function render(Input $input)
    {
        $canDelete = $this->acl->hasPermission($input->getResourcePathDelete());
        $canEdit = $this->acl->hasPermission($input->getResourcePathEdit());

        $this->addDefaultColumns($input, $canDelete, $canEdit);

        if ($this->isRequiredAjaxRequest($input)) {
            return new JsonResponse([
                'data' => $this->mapTableColumnsToDbFieldsAjax($input),
            ]);
        }

        return [
            'grid' => [
                'can_edit' => $canEdit,
                'can_delete' => $canDelete,
                'identifier' => \substr($input->getIdentifier(), 1),
                'header' => $this->renderTableHeader($input),
                'config' => $this->configProcessor->generateDataTableConfig($input),
                'results' => $this->mapTableColumnsToDbFields($input),
                'num_results' => $input->getResultsCount(),
                'show_mass_delete' => $canDelete && $input->getResultsCount() > 0,
            ],
        ];
    }

    /**
     * Checks, whether we have the required AJAX request in effect.
     *
     * @param \ACP3\Core\DataGrid\Input $input
     */
    private function isRequiredAjaxRequest(Input $input): bool
    {
        return $this->request->isXmlHttpRequest()
            && $this->request->getParameters()->get('ajax', '') === \substr($input->getIdentifier(), 1);
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     */
    private function mapTableColumnsToDbFieldsAjax(Input $input): array
    {
        $renderedResults = [];
        $totalResults = $input->getResultsCount();
        foreach ($input->getResults() as $result) {
            $row = [];
            foreach (clone $input->getColumns() as $column) {
                if ($this->container->has($column['type']) && !empty($column['label'])) {
                    /** @var ColumnRendererInterface $columnRenderer */
                    $columnRenderer = $this->container->get($column['type']);

                    $row[] = $columnRenderer
                        ->setIdentifier($input->getIdentifier())
                        ->setPrimaryKey($input->getPrimaryKey())
                        ->setUseAjax($this->isRequiredAjaxRequest($input))
                        ->setTotalResults($totalResults)
                        ->fetchDataAndRenderColumn($column, $result);
                }
            }

            $renderedResults[] = $row;
        }

        return $renderedResults;
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     *
     * @return string
     */
    protected function renderTableHeader(Input $input)
    {
        $header = '';
        foreach (clone $input->getColumns() as $column) {
            if (!empty($column['label'])) {
                $header .= $this->container->get(HeaderColumnRenderer::class)
                    ->setIdentifier($input->getIdentifier())
                    ->setPrimaryKey($input->getPrimaryKey())
                    ->fetchDataAndRenderColumn($column, []);
            }
        }

        return $header;
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     *
     * @return string
     */
    protected function mapTableColumnsToDbFields(Input $input)
    {
        if ($input->isUseAjax()) {
            return '';
        }

        $renderedResults = '';
        $totalResults = $input->getResultsCount();
        foreach ($input->getResults() as $result) {
            $renderedResults .= '<tr>';
            foreach (clone $input->getColumns() as $column) {
                if ($this->container->has($column['type']) && !empty($column['label'])) {
                    /** @var ColumnRendererInterface $columnRenderer */
                    $columnRenderer = $this->container->get($column['type']);

                    $renderedResults .= $columnRenderer
                        ->setIdentifier($input->getIdentifier())
                        ->setPrimaryKey($input->getPrimaryKey())
                        ->setUseAjax($this->isRequiredAjaxRequest($input))
                        ->setTotalResults($totalResults)
                        ->fetchDataAndRenderColumn($column, $result);
                }
            }

            $renderedResults .= "</tr>\n";
        }

        return $renderedResults;
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     *
     * @return array
     */
    protected function generateDataTableConfig(Input $input)
    {
        $columnDefinitions = [];
        $i = 0;

        $defaultSortColumn = $defaultSortDirection = null;
        foreach (clone $input->getColumns() as $column) {
            if ($column['sortable'] === false) {
                $columnDefinitions[] = $i;
            }

            if ($column['default_sort'] === true &&
                \in_array($column['default_sort_direction'], ['asc', 'desc'])
            ) {
                $defaultSortColumn = $i;
                $defaultSortDirection = $column['default_sort_direction'];
            }

            if (!empty($column['label'])) {
                ++$i;
            }
        }

        return [
            'element' => $input->getIdentifier(),
            'records_per_page' => $input->getRecordsPerPage(),
            'hide_col_sort' => \implode(', ', $columnDefinitions),
            'sort_col' => $defaultSortColumn,
            'sort_dir' => $defaultSortDirection,
        ];
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     */
    protected function addDefaultColumns(Input $input, bool $canDelete, bool $canEdit)
    {
        if ($input->isEnableMassAction() && $canDelete) {
            $input->addColumn([
                'label' => $input->getIdentifier(),
                'type' => MassActionColumnRenderer::class,
                'class' => 'datagrid-column datagrid-column__mass-action',
                'sortable' => false,
                'custom' => [
                    'can_delete' => $canDelete,
                ],
            ], 1000);
        }

        if ($input->isEnableOptions()) {
            $input->addColumn([
                'label' => $this->translator->t('system', 'action'),
                'type' => OptionColumnRenderer::class,
                'class' => 'datagrid-column datagrid-column__actions',
                'sortable' => false,
                'custom' => [
                    'can_delete' => $canDelete,
                    'can_edit' => $canEdit,
                    'resource_path_delete' => $input->getResourcePathDelete(),
                    'resource_path_edit' => $input->getResourcePathEdit(),
                ],
            ], 0);
        }
    }
}
