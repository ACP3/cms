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
    private $acl;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
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
    private $serviceLocator;

    public function __construct(
        ContainerInterface $serviceLocator,
        RequestInterface $request,
        ConfigProcessor $configProcessor,
        ACL $acl,
        Translator $translator
    ) {
        $this->acl = $acl;
        $this->translator = $translator;
        $this->configProcessor = $configProcessor;
        $this->request = $request;
        $this->serviceLocator = $serviceLocator;
    }

    /**
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
                'column_count' => \count($input->getColumns()),
                'identifier' => substr($input->getIdentifier(), 1),
                'header' => $this->renderTableHeader($input),
                'config' => $this->configProcessor->generateDataTableConfig($input),
                'results' => $this->mapTableColumnsToDbFields($input),
                'num_results' => $input->getResultsCount(),
                'show_mass_delete' => $canDelete && $input->getResultsCount() > 0,
            ],
        ];
    }

    /**
     * Checks, whether the required AJAX request is in effect.
     */
    private function isRequiredAjaxRequest(Input $input): bool
    {
        return $this->request->isXmlHttpRequest()
            && $this->request->getParameters()->get('ajax', '') === substr($input->getIdentifier(), 1);
    }

    private function mapTableColumnsToDbFieldsAjax(Input $input): array
    {
        $renderedResults = [];
        $totalResults = $input->getResultsCount();
        foreach ($input->getResults() as $result) {
            $row = [];
            foreach (clone $input->getColumns() as $column) {
                if (!empty($column['label']) && $this->serviceLocator->has($column['type'])) {
                    /** @var ColumnRendererInterface $columnRenderer */
                    $columnRenderer = $this->serviceLocator->get($column['type']);

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

    private function renderTableHeader(Input $input): string
    {
        $header = '';
        foreach (clone $input->getColumns() as $column) {
            if (!empty($column['label'])) {
                $header .= $this->serviceLocator->get(HeaderColumnRenderer::class)
                    ->setIdentifier($input->getIdentifier())
                    ->setPrimaryKey($input->getPrimaryKey())
                    ->fetchDataAndRenderColumn($column, []);
            }
        }

        return $header;
    }

    private function mapTableColumnsToDbFields(Input $input): string
    {
        if ($input->isUseAjax()) {
            return '';
        }

        $renderedResults = '';
        $totalResults = $input->getResultsCount();
        foreach ($input->getResults() as $result) {
            $renderedResults .= '<tr>';
            foreach (clone $input->getColumns() as $column) {
                if (!empty($column['label']) && $this->serviceLocator->has($column['type'])) {
                    /** @var ColumnRendererInterface $columnRenderer */
                    $columnRenderer = $this->serviceLocator->get($column['type']);

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

    private function addDefaultColumns(Input $input, bool $canDelete, bool $canEdit): void
    {
        if ($canDelete && $input->isEnableMassAction()) {
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
