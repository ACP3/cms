<?php

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Accounts
 * @package ACP3\Modules\ACP3\Newsletter\Controller\Admin
 */
class Accounts extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus
     */
    protected $accountStatusHelper;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\AccountRepository
     */
    protected $accountRepository;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext            $context
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus    $accountStatusHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Model\AccountRepository $accountRepository
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Newsletter\Helper\AccountStatus $accountStatusHelper,
        Newsletter\Model\AccountRepository $accountRepository)
    {
        parent::__construct($context);

        $this->accountStatusHelper = $accountStatusHelper;
        $this->accountRepository = $accountRepository;
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionActivate($id)
    {
        $bool = $this->accountStatusHelper->changeAccountStatus(
            Newsletter\Helper\AccountStatus::ACCOUNT_STATUS_CONFIRMED,
            $id
        );

        return $this->redirectMessages()->setMessage($bool, $this->lang->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'));
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDelete($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = false;
                foreach ($items as $item) {
                    $bool = $this->accountStatusHelper->changeAccountStatus(
                        Newsletter\Helper\AccountStatus::ACCOUNT_STATUS_DISABLED,
                        $item
                    );
                }

                return $bool;
            }
        );
    }

    /**
     * @return array
     */
    public function actionIndex()
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
                'label' => $this->lang->t('system', 'email_address'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::NAME,
                'fields' => ['mail'],
                'default_sort' => true
            ], 60)
            ->addColumn([
                'label' => $this->lang->t('newsletter', 'salutation'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\ReplaceValueColumnRenderer::NAME,
                'fields' => ['salutation'],
                'custom' => [
                    'search' => [0, 1, 2],
                    'replace' => [
                        '',
                        $this->lang->t('newsletter', 'salutation_female'),
                        $this->lang->t('newsletter', 'salutation_male'),
                    ]
                ]
            ], 50)
            ->addColumn([
                'label' => $this->lang->t('newsletter', 'first_name'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::NAME,
                'fields' => ['first_name'],
            ], 40)
            ->addColumn([
                'label' => $this->lang->t('newsletter', 'last_name'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::NAME,
                'fields' => ['last_name'],
            ], 30)
            ->addColumn([
                'label' => $this->lang->t('newsletter', 'status'),
                'type' => Newsletter\Helper\DataGrid\ColumnRenderer\AccountStatusColumnRenderer::NAME,
                'fields' => ['status'],
            ], 20)
            ->addColumn([
                'label' => $this->lang->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::NAME,
                'fields' => ['id'],
                'primary' => true
            ], 10);

        return [
            'grid' => $dataGrid->render(),
            'show_mass_delete_button' => count($accounts) > 0
        ];
    }
}
