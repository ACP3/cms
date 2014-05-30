<?php

namespace ACP3\Modules\Newsletter\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Newsletter;

/**
 * Description of NewsletterAdmin
 *
 * @author Tino Goratsch
 */
class Accounts extends Core\Modules\Controller\Admin
{

    /**
     *
     * @var Newsletter\Model
     */
    protected $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new Newsletter\Model($this->db, $this->lang, $this->auth);
    }

    public function actionActivate()
    {
        $bool = false;
        if (Core\Validate::isNumber($this->uri->id) === true) {
            $bool = $this->model->update(array('hash' => ''), $this->uri->id, Newsletter\Model::TABLE_NAME_ACCOUNTS);
        }

        Core\Functions::setRedirectMessage($bool, $this->lang->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'), 'acp/newsletter/accounts');
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/newsletter/accounts/delete', 'acp/newsletter/accounts');

        if ($this->uri->action === 'confirmed') {
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->model->delete($item, '', Newsletter\Model::TABLE_NAME_ACCOUNTS);
            }
            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/newsletter/accounts');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/index/404');
        }
    }

    public function actionIndex()
    {
        Core\Functions::getRedirectMessage();

        $accounts = $this->model->getAllAccounts();
        $c_accounts = count($accounts);

        if ($c_accounts > 0) {
            $canDelete = Core\Modules::hasPermission('admin/newsletter/accounts/delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 3 : 2,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : ''
            );
            $this->appendContent(Core\Functions::dataTable($config));

            $this->view->assign('accounts', $accounts);
            $this->view->assign('can_delete', $canDelete);
        }
    }

}