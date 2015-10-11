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

    public function actionIndex()
    {
        $accounts = $this->accountRepository->getAllAccounts();
        $c_accounts = count($accounts);

        if ($c_accounts > 0) {
            $canDelete = $this->acl->hasPermission('admin/newsletter/accounts/delete');
            $config = [
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 3 : 2,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->user->getEntriesPerPage()
            ];
            $this->view->assign('datatable_config', $config);

            $search = [0, 1, 2];
            $replace = [
                '',
                $this->lang->t('newsletter', 'salutation_female'),
                $this->lang->t('newsletter', 'salutation_male'),
            ];
            for ($i = 0; $i < $c_accounts; ++$i) {
                $accounts[$i]['salutation'] = str_replace($search, $replace, $accounts[$i]['salutation']);
            }

            $this->view->assign('accounts', $accounts);
            $this->view->assign('can_delete', $canDelete);
        }
    }
}
