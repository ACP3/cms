<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Modules\ACP3\Users;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\Repository\DataGridRepository
     */
    protected $dataGridRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Modules\ACP3\Users\Model\Repository\DataGridRepository $dataGridRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Users\Model\Repository\DataGridRepository $dataGridRepository
    ) {
        parent::__construct($context);

        $this->dataGridRepository = $dataGridRepository;
    }

    /**
     * @return array
     */
    public function execute()
    {
        /** @var Core\Helpers\DataGrid $dataGrid */
        $dataGrid = $this->get('core.helpers.data_grid');
        $dataGrid
            ->setRepository($this->dataGridRepository)
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#users-data-grid')
            ->setResourcePathDelete('admin/users/index/delete')
            ->setResourcePathEdit('admin/users/index/edit');

        $this->addDataGridColumns($dataGrid);

        return [
            'grid' => $dataGrid->render(),
            'show_mass_delete_button' => $dataGrid->countDbResults() > 0,
        ];
    }

    /**
     * @param Core\Helpers\DataGrid $dataGrid
     */
    protected function addDataGridColumns(Core\Helpers\DataGrid $dataGrid)
    {
        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('users', 'nickname'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['nickname'],
                'default_sort' => true,
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('system', 'email_address'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['mail'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('permissions', 'roles'),
                'type' => Users\Helpers\DataGrid\ColumnRenderer\UserRolesColumnRenderer::class,
                'fields' => ['id'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
            ], 10);
    }
}
