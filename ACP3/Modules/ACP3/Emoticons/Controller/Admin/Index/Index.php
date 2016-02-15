<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index
 */
class Index extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Model\EmoticonRepository
     */
    protected $emoticonRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext            $context
     * @param \ACP3\Modules\ACP3\Emoticons\Model\EmoticonRepository $emoticonRepository
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Emoticons\Model\EmoticonRepository $emoticonRepository)
    {
        parent::__construct($context);

        $this->emoticonRepository = $emoticonRepository;
    }

    public function execute()
    {
        $emoticons = $this->emoticonRepository->getAll();

        /** @var Core\Helpers\DataGrid $dataGrid */
        $dataGrid = $this->get('core.helpers.data_grid');
        $dataGrid
            ->setResults($emoticons)
            ->setRecordsPerPage($this->user->getEntriesPerPage())
            ->setIdentifier('#acp-table')
            ->setResourcePathDelete('admin/emoticons/index/delete')
            ->setResourcePathEdit('admin/emoticons/index/edit');

        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('system', 'description'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['description'],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('emoticons', 'code'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['code']
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('emoticons', 'picture'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\PictureColumnRenderer::class,
                'fields' => ['img'],
                'custom' => [
                    'pattern' => $this->appPath->getWebRoot() . 'uploads/emoticons/%s'
                ]
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
                'default_sort' => true
            ], 10);

        return [
            'grid' => $dataGrid->render(),
            'show_mass_delete_button' => count($emoticons) > 0
        ];
    }
}
