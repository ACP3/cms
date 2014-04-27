<?php

namespace ACP3\Modules\Newsletter\Controller;

use ACP3\Core;
use ACP3\Modules\Newsletter;

/**
 * Description of NewsletterAdmin
 *
 * @author Tino Goratsch
 */
class Admin extends Core\Modules\Controller\Admin
{

    /**
     *
     * @var Newsletter\Model
     */
    protected $model;

    protected function _init()
    {
        $this->model = new Newsletter\Model($this->db, $this->lang, $this->auth);
    }

    public function actionActivate()
    {
        $bool = false;
        if (Core\Validate::isNumber($this->uri->id) === true) {
            $bool = $this->model->update(array('hash' => ''), $this->uri->id, Newsletter\Model::TABLE_NAME_ACCOUNTS);
        }

        Core\Functions::setRedirectMessage($bool, $this->lang->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'), 'acp/newsletter');
    }

    public function actionCreate()
    {
        $settings = Core\Config::getSettings('newsletter');

        if (empty($_POST) === false) {
            try {
                $this->model->validate($_POST);

                // Newsletter archivieren
                $insertValues = array(
                    'id' => '',
                    'date' => $this->date->toSQL(),
                    'title' => Core\Functions::strEncode($_POST['title']),
                    'text' => Core\Functions::strEncode($_POST['text'], true),
                    'html' => $settings['html'],
                    'status' => 0,
                    'user_id' => $this->auth->getUserId(),
                );
                $lastId = $this->model->insert($insertValues);

                // Test-Newsletter
                if ($_POST['test'] == 1) {
                    $bool2 = Newsletter\Helpers::sendNewsletter($lastId, $settings['mail'], true);

                    $lang = $this->lang->t('newsletter', 'create_success');
                    $result = $lastId && $bool2;
                } else {
                    $lang = $this->lang->t('newsletter', 'save_success');
                    $result = $lastId;
                }

                $this->session->unsetFormToken();

                if ($result === false) {
                    $lang = $this->lang->t('newsletter', 'create_save_error');
                }
                Core\Functions::setRedirectMessage($result, $lang, 'acp/newsletter');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/newsletter');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $this->view->assign('settings', $settings);

        $this->view->assign('form', array_merge(array('title' => '', 'text' => ''), $_POST));

        $lang_test = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('test', Core\Functions::selectGenerator('test', array(1, 0), $lang_test, 0, 'checked'));

        $lang_action = array($this->lang->t('newsletter', 'send_and_save'), $this->lang->t('newsletter', 'only_save'));
        $this->view->assign('action', Core\Functions::selectGenerator('action', array(1, 0), $lang_action, 1, 'checked'));

        $this->session->generateFormToken();
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/newsletter/delete', 'acp/newsletter');

        if ($this->uri->action === 'confirmed') {
            $items = explode('|', $items);
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->model->delete($item);
            }
            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/newsletter');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionDeleteAccount()
    {
        $items = $this->_deleteItem('acp/newsletter/delete_account', 'acp/newsletter/list_accounts');

        if ($this->uri->action === 'confirmed') {
            $items = explode('|', $items);
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->model->delete($item, Newsletter\Model::TABLE_NAME_ACCOUNTS);
            }
            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/newsletter/list_accounts');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionEdit()
    {
        $newsletter = $this->model->getOneById($this->uri->id);
        $settings = Core\Config::getSettings('newsletter');

        if (empty($newsletter) === false) {
            if (empty($_POST) === false) {
                try {
                    $this->model->validate($_POST);

                    // Newsletter archivieren
                    $updateValues = array(
                        'date' => $this->date->toSQL(),
                        'title' => Core\Functions::strEncode($_POST['title']),
                        'text' => Core\Functions::strEncode($_POST['text'], true),
                        'user_id' => $this->auth->getUserId(),
                    );
                    $bool = $this->model->update($updateValues, $this->uri->id);

                    // Test-Newsletter
                    if ($_POST['test'] == 1) {
                        $bool2 = Newsletter\Helpers::sendNewsletter($this->uri->id, $settings['mail'], true);

                        $lang = $this->lang->t('newsletter', 'create_success');
                        $result = $bool && $bool2;
                    } else {
                        $lang = $this->lang->t('newsletter', 'save_success');
                        $result = $bool;
                    }

                    $this->session->unsetFormToken();

                    if ($result === false) {
                        $lang = $this->lang->t('newsletter', 'create_save_error');
                    }
                    Core\Functions::setRedirectMessage($result, $lang, 'acp/newsletter');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/newsletter');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

            $this->view->assign('settings', array_merge($settings, array('html' => $newsletter['html'])));

            $this->view->assign('form', array_merge($newsletter, $_POST));

            $lang_test = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('test', Core\Functions::selectGenerator('test', array(1, 0), $lang_test, 0, 'checked'));

            $lang_action = array($this->lang->t('newsletter', 'send_and_save'), $this->lang->t('newsletter', 'only_save'));
            $this->view->assign('action', Core\Functions::selectGenerator('action', array(1, 0), $lang_action, 1, 'checked'));

            $this->session->generateFormToken();
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionList()
    {
        Core\Functions::getRedirectMessage();

        $newsletter = $this->model->getAllInAcp();
        $c_newsletter = count($newsletter);

        if ($c_newsletter > 0) {
            $canDelete = Core\Modules::hasPermission('newsletter', 'acp_delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : ''
            );
            $this->view->appendContent(Core\Functions::dataTable($config));

            $search = array('0', '1');
            $replace = array($this->lang->t('newsletter', 'not_yet_sent'), $this->lang->t('newsletter', 'already_sent'));
            for ($i = 0; $i < $c_newsletter; ++$i) {
                $newsletter[$i]['date_formatted'] = $this->date->formatTimeRange($newsletter[$i]['date']);
                $newsletter[$i]['status'] = str_replace($search, $replace, $newsletter[$i]['status']);
            }
            $this->view->assign('newsletter', $newsletter);
            $this->view->assign('can_delete', $canDelete);
            $this->view->assign('can_send', Core\Modules::hasPermission('newsletter', 'acp_send'));
        }
    }

    public function actionListAccounts()
    {
        Core\Functions::getRedirectMessage();

        $accounts = $this->model->getAllAccounts();
        $c_accounts = count($accounts);

        if ($c_accounts > 0) {
            $canDelete = Core\Modules::hasPermission('newsletter', 'acp_delete_account');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 3 : 2,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : ''
            );
            $this->view->appendContent(Core\Functions::dataTable($config));

            $this->view->assign('accounts', $accounts);
            $this->view->assign('can_delete', $canDelete);
        }
    }

    public function actionSend()
    {
        if (Core\Validate::isNumber($this->uri->id) === true && $this->model->newsletterExists($this->uri->id) === true) {
            $bool = Newsletter\Helpers::sendNewsletter($this->uri->id, null, true);
            $bool2 = false;
            if ($bool === true) {
                $bool2 = $this->model->update(array('status' => '1'), $this->uri->id);
            }

            Core\Functions::setRedirectMessage($bool && $bool2, $this->lang->t('newsletter', $bool === true && $bool2 !== false ? 'create_success' : 'create_save_error'), 'acp/newsletter');
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionSettings()
    {
        if (empty($_POST) === false) {
            try {
                $this->model->validateSettings($_POST);

                $data = array(
                    'mail' => $_POST['mail'],
                    'mailsig' => Core\Functions::strEncode($_POST['mailsig']),
                    'html' => (int) $_POST['html']
                );

                $bool = Core\Config::setSettings('newsletter', $data);

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/newsletter');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/newsletter');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $settings = Core\Config::getSettings('newsletter');

        $this->view->assign('form', array_merge($settings, $_POST));

        $langHtml = array(
            $this->lang->t('system', 'yes'),
            $this->lang->t('system', 'no')
        );
        $this->view->assign('html', Core\Functions::selectGenerator('html', array(1, 0), $langHtml, $settings['html'], 'checked'));

        $this->session->generateFormToken();
    }

}