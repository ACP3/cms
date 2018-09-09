<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Comments\Model\Repository\CommentRepository
     */
    protected $commentRepository;
    /**
     * @var \ACP3\Core\DataGrid\DataGrid
     */
    private $dataGrid;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                  $context
     * @param \ACP3\Modules\ACP3\Comments\Model\Repository\CommentRepository $commentRepository
     * @param \ACP3\Core\DataGrid\DataGrid                                   $dataGrid
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Comments\Model\Repository\CommentRepository $commentRepository,
        Core\DataGrid\DataGrid $dataGrid
    ) {
        parent::__construct($context);

        $this->commentRepository = $commentRepository;
        $this->dataGrid = $dataGrid;
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute()
    {
        $input = (new Core\DataGrid\Input())
            ->setResults($this->commentRepository->getCommentsGroupedByModule())
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#comments-data-grid')
            ->setResourcePathDelete('admin/comments/index/delete')
            ->setResourcePathEdit('admin/comments/details/index');

        $this->addDataGridColumns($input);

        return [
            'grid' => $this->dataGrid->render($input),
            'show_mass_delete_button' => $input->getResultsCount() > 0,
        ];
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     */
    protected function addDataGridColumns(Core\DataGrid\Input $input)
    {
        $input
            ->addColumn([
                'label' => $this->translator->t('comments', 'module'),
                'type' => Core\DataGrid\ColumnRenderer\TranslateColumnRenderer::class,
                'fields' => ['module'],
                'default_sort' => true,
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('comments', 'comments_count'),
                'type' => Core\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['comments_count'],
            ], 20)
            ->addColumn([
                'fields' => ['module_id'],
                'primary' => true,
            ], 10);
    }
}
