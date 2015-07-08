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
     * @var Newsletter\Model
     */
    protected $newsletterModel;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param Newsletter\Model $newsletterModel
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Newsletter\Model $newsletterModel)
    {
        parent::__construct($context);

        $this->newsletterModel = $newsletterModel;
    }

    public function actionActivate()
    {
        $bool = false;
        if ($this->get('core.validator.rules.misc')->isNumber($this->request->getParameters()->get('id')) === true) {
            $bool = $this->newsletterModel->update(['hash' => ''], $this->request->getParameters()->get('id'), Newsletter\Model::TABLE_NAME_ACCOUNTS);
        }

        $this->redirectMessages()->setMessage($bool, $this->lang->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'));
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem();

        if ($this->request->getParameters()->get('action') === 'confirmed') {
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->newsletterModel->delete($item, '', Newsletter\Model::TABLE_NAME_ACCOUNTS);
            }

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'));
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $accounts = $this->newsletterModel->getAllAccounts();

        if (count($accounts) > 0) {
            $canDelete = $this->acl->hasPermission('admin/newsletter/accounts/delete');
            $config = [
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 3 : 2,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->auth->entries
            ];
            $this->view->assign('datatable_config', $config);
            $this->view->assign('accounts', $accounts);
            $this->view->assign('can_delete', $canDelete);
        }
    }
}
