<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Details;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var Core\Model\Repository\ModuleAwareRepositoryInterface
     */
    protected $systemModuleRepository;
    /**
     * @var \ACP3\Core\DataGrid\DataGrid
     */
    private $dataGrid;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Model\Repository\CommentsDataGridRepository
     */
    private $dataGridRepository;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Comments\Model\Repository\CommentsDataGridRepository $dataGridRepository,
        Core\Model\Repository\ModuleAwareRepositoryInterface $systemModuleRepository,
        Core\DataGrid\DataGrid $dataGrid
    ) {
        parent::__construct($context);

        $this->systemModuleRepository = $systemModuleRepository;
        $this->dataGrid = $dataGrid;
        $this->dataGridRepository = $dataGridRepository;
    }

    /**
     * @param int $id
     *
     * @return array
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute(int $id)
    {
        $input = (new Core\DataGrid\Input())
            ->setRepository($this->dataGridRepository)
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#comments-details-data-grid')
            ->setResourcePathDelete('admin/comments/details/delete/id_' . $id)
            ->setResourcePathEdit('admin/comments/details/edit')
            ->setQueryOptions(new Core\DataGrid\QueryOption('module_id', $id));

        if ($input->getResultsCount() > 0) {
            $moduleName = $this->systemModuleRepository->getModuleNameById($id);

            $this->breadcrumb->append($this->translator->t($moduleName, $moduleName));

            $this->addDataGridColumns($input);

            return [
                'grid' => $this->dataGrid->render($input),
                'module_id' => $id,
                'show_mass_delete_button' => $input->getResultsCount() > 0,
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     */
    protected function addDataGridColumns(Core\DataGrid\Input $input)
    {
        $input
            ->addColumn([
                'label' => $this->translator->t('system', 'date'),
                'type' => Core\DataGrid\ColumnRenderer\DateColumnRenderer::class,
                'fields' => ['date'],
                'default_sort' => true,
            ], 50)
            ->addColumn([
                'label' => $this->translator->t('system', 'name'),
                'type' => Core\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['name'],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('system', 'message'),
                'type' => Core\DataGrid\ColumnRenderer\Nl2pColumnRenderer::class,
                'fields' => ['message'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('comments', 'ip'),
                'type' => Core\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['ip'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
            ], 10);
    }
}
