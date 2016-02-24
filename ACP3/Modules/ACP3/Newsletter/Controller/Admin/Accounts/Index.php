<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Accounts;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Newsletter\Controller\Admin\Accounts
 */
class Index extends Core\Controller\AdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\AccountRepository
     */
    protected $accountRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext            $context
     * @param \ACP3\Modules\ACP3\Newsletter\Model\AccountRepository $accountRepository
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Newsletter\Model\AccountRepository $accountRepository)
    {
        parent::__construct($context);

        $this->accountRepository = $accountRepository;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $accounts = $this->accountRepository->getAllAccounts();

        /** @var Core\Helpers\DataGrid $dataGrid */
        $dataGrid = $this->get('core.helpers.data_grid');
        $dataGrid
            ->setResults($accounts)
            ->setRecordsPerPage($this->user->getEntriesPerPage())
            ->setIdentifier('#acp-table')
            ->setResourcePathDelete('admin/newsletter/accounts/delete');

        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('system', 'email_address'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['mail'],
                'default_sort' => true
            ], 60)
            ->addColumn([
                'label' => $this->translator->t('newsletter', 'salutation'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\ReplaceValueColumnRenderer::class,
                'fields' => ['salutation'],
                'custom' => [
                    'search' => [0, 1, 2],
                    'replace' => [
                        '',
                        $this->translator->t('newsletter', 'salutation_female'),
                        $this->translator->t('newsletter', 'salutation_male'),
                    ]
                ]
            ], 50)
            ->addColumn([
                'label' => $this->translator->t('newsletter', 'first_name'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['first_name'],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('newsletter', 'last_name'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['last_name'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('newsletter', 'status'),
                'type' => Newsletter\Helper\DataGrid\ColumnRenderer\AccountStatusColumnRenderer::class,
                'fields' => ['status'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true
            ], 10);

        return [
            'grid' => $dataGrid->render(),
            'show_mass_delete_button' => count($accounts) > 0
        ];
    }
}
