<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Details;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\System;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Comments\Controller\Admin\Details
 */
class Index extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Comments\Model\Repository\CommentRepository
     */
    protected $commentRepository;
    /**
     * @var Core\Model\Repository\ModuleAwareRepositoryInterface
     */
    protected $systemModuleRepository;

    /**
     * Index constructor.
     * @param Core\Controller\Context\AdminContext $context
     * @param Comments\Model\Repository\CommentRepository $commentRepository
     * @param Core\Model\Repository\ModuleAwareRepositoryInterface $systemModuleRepository
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Comments\Model\Repository\CommentRepository $commentRepository,
        Core\Model\Repository\ModuleAwareRepositoryInterface $systemModuleRepository)
    {
        parent::__construct($context);

        $this->commentRepository = $commentRepository;
        $this->systemModuleRepository = $systemModuleRepository;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $comments = $this->commentRepository->getAllByModuleInAcp($id);

        if (empty($comments) === false) {
            $moduleName = $this->systemModuleRepository->getModuleNameById($id);

            $this->breadcrumb->append($this->translator->t($moduleName, $moduleName));

            /** @var Core\Helpers\DataGrid $dataGrid */
            $dataGrid = $this->get('core.helpers.data_grid');
            $dataGrid
                ->setResults($comments)
                ->setRecordsPerPage($this->user->getEntriesPerPage())
                ->setIdentifier('#acp-table')
                ->setResourcePathDelete('admin/comments/details/delete/id_' . $id)
                ->setResourcePathEdit('admin/comments/details/edit');

            $dataGrid
                ->addColumn([
                    'label' => $this->translator->t('system', 'date'),
                    'type' => Core\Helpers\DataGrid\ColumnRenderer\DateColumnRenderer::class,
                    'fields' => ['date'],
                    'default_sort' => true
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
                    'primary' => true
                ], 10);

            return [
                'grid' => $dataGrid->render(),
                'module_id' => $id,
                'show_mass_delete_button' => $dataGrid->countDbResults() > 0
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
