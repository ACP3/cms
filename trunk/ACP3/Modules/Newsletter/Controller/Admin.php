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
        $this->model = new Newsletter\Model($this->db, $this->lang);
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
        if (isset($_POST['submit']) === true) {
            try {
                $this->model->validate($_POST);

                $settings = Core\Config::getSettings('newsletter');

                // Newsletter archivieren
                $insertValues = array(
                    'id' => '',
                    'date' => $this->date->toSQL(),
                    'title' => Core\Functions::strEncode($_POST['title']),
                    'text' => Core\Functions::strEncode($_POST['text'], true),
                    'status' => $_POST['test'] == 1 ? '0' : (int)$_POST['action'],
                    'user_id' => $this->auth->getUserId(),
                );
                $bool = $this->model->insert($insertValues);

                if ($_POST['action'] == 1 && $bool !== false) {
                    $subject = Core\Functions::strEncode($_POST['title'], true);
                    $body = Core\Functions::strEncode($_POST['text'], true) . "\n-- \n" . html_entity_decode($settings['mailsig'], ENT_QUOTES, 'UTF-8');

                    // Test-Newsletter
                    if ($_POST['test'] == 1) {
                        $bool2 = Core\Functions::generateEmail('', $settings['mail'], $settings['mail'], $subject, $body);
                    } else { // An alle versenden
                        $bool2 = Newsletter\Helpers::sendNewsletter($subject, $body, $settings['mail']);
                    }
                }

                $this->session->unsetFormToken();

                if ($_POST['action'] == 0 && $bool !== false) {
                    Core\Functions::setRedirectMessage(true, $this->lang->t('newsletter', 'save_success'), 'acp/newsletter');
                } elseif ($_POST['action'] == 1 && $bool !== false && $bool2 === true) {
                    Core\Functions::setRedirectMessage($bool && $bool2, $this->lang->t('newsletter', 'create_success'), 'acp/newsletter');
                } else {
                    Core\Functions::setRedirectMessage(false, $this->lang->t('newsletter', 'create_save_error'), 'acp/newsletter');
                }
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/newsletter');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $this->view->assign('form', isset($_POST['submit']) ? $_POST : array('title' => '', 'text' => ''));

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

        if (empty($newsletter) === false) {
            // Brotkrümelspur
            $this->breadcrumb
                ->append($this->lang->t('newsletter', 'newsletter'), $this->uri->route('acp/newsletter'))
                ->append($this->lang->t('newsletter', 'acp_edit'));

            if (isset($_POST['submit']) === true) {
                try {
                    $this->model->validate($_POST);

                    $settings = Core\Config::getSettings('newsletter');

                    // Newsletter archivieren
                    $updateValues = array(
                        'date' => $this->date->toSQL(),
                        'title' => Core\Functions::strEncode($_POST['title']),
                        'text' => Core\Functions::strEncode($_POST['text'], true),
                        'status' => $_POST['test'] == 1 ? '0' : (int)$_POST['action'],
                        'user_id' => $this->auth->getUserId(),
                    );
                    $bool = $this->model->update($updateValues, $this->uri->id);

                    if ($_POST['action'] == 1 && $bool !== false) {
                        $subject = Core\Functions::strEncode($_POST['title'], true);
                        $body = Core\Functions::strEncode($_POST['text'], true) . "\n" . html_entity_decode($settings['mailsig'], ENT_QUOTES, 'UTF-8');

                        // Testnewsletter
                        if ($_POST['test'] == 1) {
                            $bool2 = Core\Functions::generateEmail('', $settings['mail'], $settings['mail'], $subject, $body);
                            // An alle versenden
                        } else {
                            $bool2 = Newsletter\Helpers::sendNewsletter($subject, $body, $settings['mail']);
                        }
                    }

                    $this->session->unsetFormToken();

                    if ($_POST['action'] == 0 && $bool !== false) {
                        Core\Functions::setRedirectMessage(true, $this->lang->t('newsletter', 'save_success'), 'acp/newsletter');
                    } elseif ($_POST['action'] == 1 && $bool !== false && $bool2 === true) {
                        Core\Functions::setRedirectMessage($bool && $bool2, $this->lang->t('newsletter', 'create_success'), 'acp/newsletter');
                    } else {
                        Core\Functions::setRedirectMessage(false, $this->lang->t('newsletter', 'create_save_error'), 'acp/newsletter');
                    }
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/newsletter');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

            $this->view->assign('form', isset($_POST['submit']) ? $_POST : $newsletter);

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
            $can_delete = Core\Modules::hasPermission('newsletter', 'acp_delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $can_delete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $can_delete === true ? 0 : ''
            );
            $this->view->appendContent(Core\Functions::dataTable($config));

            $search = array('0', '1');
            $replace = array($this->lang->t('newsletter', 'not_yet_sent'), $this->lang->t('newsletter', 'already_sent'));
            for ($i = 0; $i < $c_newsletter; ++$i) {
                $newsletter[$i]['date_formatted'] = $this->date->formatTimeRange($newsletter[$i]['date']);
                $newsletter[$i]['status'] = str_replace($search, $replace, $newsletter[$i]['status']);
            }
            $this->view->assign('newsletter', $newsletter);
            $this->view->assign('can_delete', $can_delete);
            $this->view->assign('can_send', Core\Modules::hasPermission('newsletter', 'acp_send'));
        }
    }

    public function actionListAccounts()
    {
        Core\Functions::getRedirectMessage();

        $accounts = $this->model->getAllAccounts();
        $c_accounts = count($accounts);

        if ($c_accounts > 0) {
            $can_delete = Core\Modules::hasPermission('newsletter', 'acp_delete_account');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $can_delete === true ? 3 : 2,
                'sort_dir' => 'desc',
                'hide_col_sort' => $can_delete === true ? 0 : ''
            );
            $this->view->appendContent(Core\Functions::dataTable($config));

            $this->view->assign('accounts', $accounts);
            $this->view->assign('can_delete', $can_delete);
        }
    }

    public function actionSend()
    {
        if (Core\Validate::isNumber($this->uri->id) === true && $this->model->newsletterExists($this->uri->id) === true) {
            $settings = Core\Config::getSettings('newsletter');
            $newsletter = $this->model->getOneById($this->uri->id);

            $subject = html_entity_decode($newsletter['title'], ENT_QUOTES, 'UTF-8');
            $body = html_entity_decode($newsletter['text'] . "\n-- \n" . $settings['mailsig'], ENT_QUOTES, 'UTF-8');

            $bool = Newsletter\Helpers::sendNewsletter($subject, $body, $settings['mail']);
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
        if (isset($_POST['submit']) === true) {
            try {
                $this->model->validateSettings($_POST);

                $data = array(
                    'mail' => $_POST['mail'],
                    'mailsig' => Core\Functions::strEncode($_POST['mailsig'])
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

        $this->view->assign('form', isset($_POST['submit']) ? $_POST : $settings);

        $this->session->generateFormToken();
    }

}