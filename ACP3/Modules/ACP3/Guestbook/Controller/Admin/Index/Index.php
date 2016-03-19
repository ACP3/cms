<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons;
use ACP3\Modules\ACP3\Guestbook;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Guestbook\Controller\Admin\Index
 */
class Index extends Core\Controller\AdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Model\DataGridRepository
     */
    protected $dataGridRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext            $context
     * @param \ACP3\Modules\ACP3\Guestbook\Model\DataGridRepository $dataGridRepository
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Guestbook\Model\DataGridRepository $dataGridRepository
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
            ->setRecordsPerPage($this->user->getEntriesPerPage())
            ->setIdentifier('#acp-table')
            ->setResourcePathDelete('admin/guestbook/index/delete')
            ->setResourcePathEdit('admin/guestbook/index/edit');

        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('system', 'date'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\DateColumnRenderer::class,
                'fields' => ['date'],
                'default_sort' => true,
                'default_sort_direction' => 'desc'
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
                'label' => $this->translator->t('guestbook', 'ip'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['ip'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true
            ], 10);

        return [
            'grid' => $dataGrid->render(),
            'show_mass_delete_button' => $dataGrid->countDbResults() > 0
        ];
    }
}
