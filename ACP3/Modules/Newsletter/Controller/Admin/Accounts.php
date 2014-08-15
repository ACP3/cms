<?php

namespace ACP3\Modules\Newsletter\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Newsletter;

/**
 * Class Accounts
 * @package ACP3\Modules\Newsletter\Controller\Admin
 */
class Accounts extends Core\Modules\Controller\Admin
{

    /**
     * @var Newsletter\Model
     */
    protected $newsletterModel;

    public function __construct(
        Core\Context\Admin $context,
        Newsletter\Model $newsletterModel)
    {
        parent::__construct($context);

        $this->newsletterModel = $newsletterModel;
    }

    public function actionActivate()
    {
        $bool = false;
        if ($this->get('core.validator.rules.misc')->isNumber($this->request->id) === true) {
            $bool = $this->newsletterModel->update(array('hash' => ''), $this->request->id, Newsletter\Model::TABLE_NAME_ACCOUNTS);
        }

        $this->redirectMessages()->setMessage($bool, $this->lang->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'), 'acp/newsletter/accounts');
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/newsletter/accounts/delete', 'acp/newsletter/accounts');

        if ($this->request->action === 'confirmed') {
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->newsletterModel->delete($item, '', Newsletter\Model::TABLE_NAME_ACCOUNTS);
            }

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/newsletter/accounts');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $this->redirectMessages()->getMessage();

        $accounts = $this->newsletterModel->getAllAccounts();
        $c_accounts = count($accounts);

        if ($c_accounts > 0) {
            $canDelete = $this->modules->hasPermission('admin/newsletter/accounts/delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 3 : 2,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : ''
            );
            $this->appendContent($this->get('core.functions')->dataTable($config));

            $this->view->assign('accounts', $accounts);
            $this->view->assign('can_delete', $canDelete);
        }
    }

}