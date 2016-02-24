<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Users\Controller\Admin\Index
 */
class Index extends Core\Controller\AdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserRepository
     */
    protected $userRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext    $context
     * @param \ACP3\Modules\ACP3\Users\Model\UserRepository $userRepository
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Users\Model\UserRepository $userRepository)
    {
        parent::__construct($context);

        $this->userRepository = $userRepository;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $users = $this->userRepository->getAllInAcp();

        /** @var Core\Helpers\DataGrid $dataGrid */
        $dataGrid = $this->get('core.helpers.data_grid');
        $dataGrid
            ->setResults($users)
            ->setRecordsPerPage($this->user->getEntriesPerPage())
            ->setIdentifier('#acp-table')
            ->setResourcePathDelete('admin/users/index/delete')
            ->setResourcePathEdit('admin/users/index/edit');

        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('users', 'nickname'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['nickname'],
                'default_sort' => true
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
                'primary' => true
            ], 10);

        return [
            'grid' => $dataGrid->render(),
            'show_mass_delete_button' => count($users) > 0
        ];
    }
}
