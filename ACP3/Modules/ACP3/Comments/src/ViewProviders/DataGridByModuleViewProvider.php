<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\ViewProviders;

use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\DataGrid\ColumnRenderer\DateColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\IntegerColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\Nl2pColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TextColumnRenderer;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Core\DataGrid\Input;
use ACP3\Core\DataGrid\QueryOption;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Repository\ModuleAwareRepositoryInterface;
use ACP3\Modules\ACP3\Comments\Repository\CommentsDataGridRepository;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataGridByModuleViewProvider
{
    public function __construct(private readonly CommentsDataGridRepository $dataGridRepository, private readonly DataGrid $dataGrid, private readonly ModuleAwareRepositoryInterface $systemModuleRepository, private readonly RequestInterface $request, private readonly ResultsPerPage $resultsPerPage, private readonly Steps $breadcrumb, private readonly Translator $translator)
    {
    }

    /**
     * @return array<string, array<string, mixed>>|JsonResponse
     */
    public function __invoke(int $moduleId): array|JsonResponse
    {
        $dataGrid = $this->dataGrid->render($this->configureDataGrid($moduleId));

        if ($dataGrid instanceof JsonResponse) {
            return $dataGrid;
        }

        $moduleName = $this->systemModuleRepository->getModuleNameById($moduleId);

        $this->breadcrumb->append(
            $this->translator->t($moduleName, $moduleName),
            'acp/' . $this->request->getQuery()
        );

        return [...$dataGrid, ...['module_id' => $moduleId]];
    }

    private function configureDataGrid(int $moduleId): Input
    {
        return (new Input())
            ->setUseAjax(true)
            ->setRepository($this->dataGridRepository)
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#comments-details-data-grid')
            ->setResourcePathDelete('admin/comments/details/delete/id_' . $moduleId)
            ->setResourcePathEdit('admin/comments/details/edit')
            ->setQueryOptions(new QueryOption('module_id', (string) $moduleId))
            ->addColumn([
                'label' => $this->translator->t('system', 'date'),
                'type' => DateColumnRenderer::class,
                'fields' => ['date'],
                'default_sort' => true,
            ], 50)
            ->addColumn([
                'label' => $this->translator->t('system', 'name'),
                'type' => TextColumnRenderer::class,
                'fields' => ['name'],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('system', 'message'),
                'type' => Nl2pColumnRenderer::class,
                'fields' => ['message'],
                'class' => 'w-100',
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('comments', 'ip'),
                'type' => TextColumnRenderer::class,
                'fields' => ['ip'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
            ], 10);
    }
}
