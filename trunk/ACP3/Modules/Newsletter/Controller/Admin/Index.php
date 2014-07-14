<?php

namespace ACP3\Modules\Newsletter\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Newsletter;

/**
 * Class Index
 * @package ACP3\Modules\Newsletter\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{

    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var Newsletter\Model
     */
    protected $newsletterModel;

    public function __construct(
        Core\Context $context,
        Core\Breadcrumb $breadcrumb,
        Core\SEO $seo,
        Core\Validate $validate,
        Core\Session $session,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        Newsletter\Model $newsletterModel)
    {
        parent::__construct($context, $breadcrumb, $seo, $validate, $session);

        $this->date = $date;
        $this->db = $db;
        $this->newsletterModel = $newsletterModel;
    }

    public function actionCreate()
    {
        $config = new Core\Config($this->db, 'newsletter');
        $settings = $config->getSettings();

        if (empty($_POST) === false) {
            try {
                $validator = $this->get('newsletter.validator');
                $validator->validate($_POST);

                // Newsletter archivieren
                $insertValues = array(
                    'id' => '',
                    'date' => $this->date->toSQL($_POST['date']),
                    'title' => Core\Functions::strEncode($_POST['title']),
                    'text' => Core\Functions::strEncode($_POST['text'], true),
                    'html' => $settings['html'],
                    'status' => 0,
                    'user_id' => $this->auth->getUserId(),
                );
                $lastId = $this->newsletterModel->insert($insertValues);

                // Test-Newsletter
                if ($_POST['test'] == 1) {
                    $bool2 = $this->get('newsletter.helpers')->sendNewsletter($lastId, $settings['mail']);

                    $lang = $this->lang->t('newsletter', 'create_success');
                    $result = $lastId !== false && $bool2 !== false;
                } else {
                    $lang = $this->lang->t('newsletter', 'save_success');
                    $result = $lastId !== false;
                }

                $this->session->unsetFormToken();

                if ($result === false) {
                    $lang = $this->lang->t('newsletter', 'create_save_error');
                }
                $this->redirectMessages()->setMessage($result, $lang, 'acp/newsletter');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/newsletter');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
            }
        }

        $this->view->assign('date', $this->date->datepicker('date'));
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
        $items = $this->_deleteItem('acp/newsletter/index/delete', 'acp/newsletter');

        if ($this->uri->action === 'confirmed') {
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->newsletterModel->delete($item);
            }

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/newsletter');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $newsletter = $this->newsletterModel->getOneById($this->uri->id);

        if (empty($newsletter) === false) {
            $config = new Core\Config($this->db, 'newsletter');
            $settings = $config->getSettings();

            if (empty($_POST) === false) {
                try {
                    $validator = $this->get('newsletter.validator');
                    $validator->validate($_POST);

                    // Newsletter archivieren
                    $updateValues = array(
                        'date' => $this->date->toSQL($_POST['date']),
                        'title' => Core\Functions::strEncode($_POST['title']),
                        'text' => Core\Functions::strEncode($_POST['text'], true),
                        'user_id' => $this->auth->getUserId(),
                    );
                    $bool = $this->newsletterModel->update($updateValues, $this->uri->id);

                    // Test-Newsletter
                    if ($_POST['test'] == 1) {
                        $bool2 = $this->get('newsletter.helpers')->sendNewsletter($this->uri->id, $settings['mail']);

                        $lang = $this->lang->t('newsletter', 'create_success');
                        $result = $bool !== false && $bool2;
                    } else {
                        $lang = $this->lang->t('newsletter', 'save_success');
                        $result = $bool !== false;
                    }

                    $this->session->unsetFormToken();

                    if ($result === false) {
                        $lang = $this->lang->t('newsletter', 'create_save_error');
                    }

                    $this->redirectMessages()->setMessage($result, $lang, 'acp/newsletter');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/newsletter');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                    $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
                }
            }

            $this->view->assign('date', $this->date->datepicker('date', $newsletter['date']));
            $this->view->assign('settings', array_merge($settings, array('html' => $newsletter['html'])));
            $this->view->assign('form', array_merge($newsletter, $_POST));

            $lang_test = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('test', Core\Functions::selectGenerator('test', array(1, 0), $lang_test, 0, 'checked'));

            $lang_action = array($this->lang->t('newsletter', 'send_and_save'), $this->lang->t('newsletter', 'only_save'));
            $this->view->assign('action', Core\Functions::selectGenerator('action', array(1, 0), $lang_action, 1, 'checked'));

            $this->session->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $this->redirectMessages()->getMessage();

        $newsletter = $this->newsletterModel->getAllInAcp();
        $c_newsletter = count($newsletter);

        if ($c_newsletter > 0) {
            $canDelete = $this->modules->hasPermission('admin/newsletter/index/delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : ''
            );
            $this->appendContent($this->get('core.functions')->dataTable($config));

            $search = array('0', '1');
            $replace = array($this->lang->t('newsletter', 'not_yet_sent'), $this->lang->t('newsletter', 'already_sent'));
            for ($i = 0; $i < $c_newsletter; ++$i) {
                $newsletter[$i]['date_formatted'] = $this->date->formatTimeRange($newsletter[$i]['date']);
                $newsletter[$i]['status'] = str_replace($search, $replace, $newsletter[$i]['status']);
            }
            $this->view->assign('newsletter', $newsletter);
            $this->view->assign('can_delete', $canDelete);
            $this->view->assign('can_send', $this->modules->hasPermission('admin/newsletter/index/send'));
        }
    }

    public function actionSend()
    {
        if ($this->get('core.validate')->isNumber($this->uri->id) === true && $this->newsletterModel->newsletterExists($this->uri->id) === true) {
            $accounts = $this->newsletterModel->getAllActiveAccounts();
            $c_accounts = count($accounts);
            $recipients = array();

            for ($i = 0; $i < $c_accounts; ++$i) {
                $recipients[] = $accounts[$i]['mail'];
            }

            $bool = $this->get('newsletter.helpers')->sendNewsletter($this->uri->id, $recipients);
            $bool2 = false;
            if ($bool === true) {
                $bool2 = $this->newsletterModel->update(array('status' => '1'), $this->uri->id);
            }

            $this->redirectMessages()->setMessage($bool && $bool2 !== false, $this->lang->t('newsletter', $bool === true && $bool2 !== false ? 'create_success' : 'create_save_error'), 'acp/newsletter');
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionSettings()
    {
        $config = new Core\Config($this->db, 'newsletter');

        if (empty($_POST) === false) {
            try {
                $validator = $this->get('newsletter.validator');
                $validator->validateSettings($_POST);

                $data = array(
                    'mail' => $_POST['mail'],
                    'mailsig' => Core\Functions::strEncode($_POST['mailsig'], true),
                    'html' => (int) $_POST['html']
                );

                $bool = $config->setSettings($data);

                $this->session->unsetFormToken();

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/newsletter');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/newsletter');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
            }
        }

        $settings = $config->getSettings();

        $this->view->assign('form', array_merge($settings, $_POST));

        $langHtml = array(
            $this->lang->t('system', 'yes'),
            $this->lang->t('system', 'no')
        );
        $this->view->assign('html', Core\Functions::selectGenerator('html', array(1, 0), $langHtml, $settings['html'], 'checked'));

        $this->session->generateFormToken();
    }

}