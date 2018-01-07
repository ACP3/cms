<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\View\Block\Admin;

use ACP3\Core;
use ACP3\Core\Helpers\DataGrid;
use ACP3\Core\View\Block\AbstractDataGridBlock;
use ACP3\Core\View\Block\Context;
use ACP3\Modules\ACP3\Comments\Installer\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;

class CommentsDataGridBlock extends AbstractDataGridBlock
{
    /**
     * @var Core\Model\Repository\ModuleAwareRepositoryInterface
     */
    private $systemRepository;

    /**
     * CommentsDataGridBlock constructor.
     *
     * @param Context\DataGridBlockContext                         $context
     * @param Core\Model\Repository\ModuleAwareRepositoryInterface $systemRepository
     */
    public function __construct(
        Context\DataGridBlockContext $context,
        Core\Model\Repository\ModuleAwareRepositoryInterface $systemRepository
    ) {
        parent::__construct($context);

        $this->systemRepository = $systemRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function addDataGridColumns(DataGrid $dataGrid)
    {
        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('system', 'date'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\DateColumnRenderer::class,
                'fields' => ['date'],
                'default_sort' => true,
                'default_sort_direction' => 'desc',
            ], 50)
            ->addColumn([
                'label' => $this->translator->t('system', 'name'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['name'],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('system', 'message'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\Nl2pColumnRenderer::class,
                'fields' => ['message'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('comments', 'ip'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['ip'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
            ], 10);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $data = $this->getData();

        $moduleName = $this->systemRepository->getModuleNameById($data['moduleId']);

        $this->breadcrumb->append($this->translator->t($moduleName, $moduleName));

        $dataGrid = $this->getCurrentDataGrid();
        $this->configureDataGrid($dataGrid, [
            'ajax' => true,
            'identifier' => '#comments-details-data-grid',
            'resource_path_delete' => 'admin/comments/details/delete/id_' . $data['moduleId'],
            'resource_path_edit' => 'admin/comments/details/edit',
            'query_options' => [
                new DataGrid\QueryOption('module_id', $data['moduleId']),
            ],
        ]);

        $grid = $dataGrid->render();
        if ($grid instanceof JsonResponse) {
            return $grid;
        }

        return [
            'grid' => $grid,
            'module_id' => $data['moduleId'],
            'show_mass_delete_button' => $dataGrid->countDbResults() > 0,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }
}
