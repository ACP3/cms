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
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var Newsletter\Model
     */
    protected $newsletterModel;
    /**
     * @var Core\Config
     */
    protected $newsletterConfig;
    /**
     * @var Newsletter\Helpers
     */
    protected $newsletterHelpers;

    /**
     * @param Core\Context\Admin $context
     * @param Core\Date $date
     * @param Core\Helpers\Secure $secureHelper
     * @param Newsletter\Model $newsletterModel
     * @param Core\Config $newsletterConfig
     * @param Newsletter\Helpers $newsletterHelpers
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        Core\Helpers\Secure $secureHelper,
        Newsletter\Model $newsletterModel,
        Core\Config $newsletterConfig,
        Newsletter\Helpers $newsletterHelpers)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->secureHelper = $secureHelper;
        $this->newsletterModel = $newsletterModel;
        $this->newsletterConfig = $newsletterConfig;
        $this->newsletterHelpers = $newsletterHelpers;
    }

    public function actionCreate()
    {
        $settings = $this->newsletterConfig->getSettings();

        if (empty($_POST) === false) {
            $this->_createPost($_POST, $settings);
        }

        $this->view->assign('date', $this->date->datepicker('date'));
        $this->view->assign('settings', $settings);
        $this->view->assign('form', array_merge(['title' => '', 'text' => ''], $_POST));

        $lang_test = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('test', $this->get('core.helpers.forms')->selectGenerator('test', [1, 0], $lang_test, 0, 'checked'));

        $lang_action = [$this->lang->t('newsletter', 'send_and_save'), $this->lang->t('newsletter', 'only_save')];
        $this->view->assign('action', $this->get('core.helpers.forms')->selectGenerator('action', [1, 0], $lang_action, 1, 'checked'));

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/newsletter/index/delete', 'acp/newsletter');

        if ($this->request->action === 'confirmed') {
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
        $newsletter = $this->newsletterModel->getOneById($this->request->id);

        if (empty($newsletter) === false) {
            $settings = $this->newsletterConfig->getSettings();

            if (empty($_POST) === false) {
                $this->_editPost($_POST, $settings);
            }

            $this->view->assign('date', $this->date->datepicker('date', $newsletter['date']));
            $this->view->assign('settings', array_merge($settings, ['html' => $newsletter['html']]));
            $this->view->assign('form', array_merge($newsletter, $_POST));

            $lang_test = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
            $this->view->assign('test', $this->get('core.helpers.forms')->selectGenerator('test', [1, 0], $lang_test, 0, 'checked'));

            $lang_action = [$this->lang->t('newsletter', 'send_and_save'), $this->lang->t('newsletter', 'only_save')];
            $this->view->assign('action', $this->get('core.helpers.forms')->selectGenerator('action', [1, 0], $lang_action, 1, 'checked'));

            $this->secureHelper->generateFormToken($this->request->query);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $newsletter = $this->newsletterModel->getAllInAcp();

        if (count($newsletter) > 0) {
            $canDelete = $this->acl->hasPermission('admin/newsletter/index/delete');
            $config = [
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->auth->entries
            ];
            $this->view->assign('datatable_config', $config);
            $this->view->assign('newsletter', $newsletter);
            $this->view->assign('can_delete', $canDelete);
            $this->view->assign('can_send', $this->acl->hasPermission('admin/newsletter/index/send'));
            $this->view->assign('has_active_newsletter_accounts', $this->newsletterModel->countAllActiveAccounts() > 0);
        }
    }

    public function actionSend()
    {
        if ($this->get('core.validator.rules.misc')->isNumber($this->request->id) === true &&
            $this->newsletterModel->newsletterExists($this->request->id) === true) {
            $accounts = $this->newsletterModel->getAllActiveAccounts();
            $c_accounts = count($accounts);
            $recipients = [];

            for ($i = 0; $i < $c_accounts; ++$i) {
                $recipients[] = $accounts[$i]['mail'];
            }

            $bool = $this->newsletterHelpers->sendNewsletter($this->request->id, $recipients);
            $bool2 = false;
            if ($bool === true) {
                $bool2 = $this->newsletterModel->update(['status' => '1'], $this->request->id);
            }

            $this->redirectMessages()->setMessage($bool && $bool2 !== false, $this->lang->t('newsletter', $bool === true && $bool2 !== false ? 'create_success' : 'create_save_error'), 'acp/newsletter');
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionSettings()
    {
        if (empty($_POST) === false) {
            $this->_settingsPost($_POST);
        }

        $settings = $this->newsletterConfig->getSettings();

        $this->view->assign('form', array_merge($settings, $_POST));

        $langHtml = [
            $this->lang->t('system', 'yes'),
            $this->lang->t('system', 'no')
        ];
        $this->view->assign('html', $this->get('core.helpers.forms')->selectGenerator('html', [1, 0], $langHtml, $settings['html'], 'checked'));

        $this->secureHelper->generateFormToken($this->request->query);
    }

    /**
     * @param array $formData
     * @param array $settings
     */
    private function _createPost(array $formData, array $settings)
    {
        try {
            $validator = $this->get('newsletter.validator');
            $validator->validate($formData);

            // Newsletter archivieren
            $insertValues = [
                'id' => '',
                'date' => $this->date->toSQL($formData['date']),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'html' => $settings['html'],
                'status' => 0,
                'user_id' => $this->auth->getUserId(),
            ];
            $lastId = $this->newsletterModel->insert($insertValues);

            // Test-Newsletter
            if ($formData['test'] == 1) {
                $bool2 = $this->newsletterHelpers->sendNewsletter($lastId, $settings['mail']);

                $lang = $this->lang->t('newsletter', 'create_success');
                $result = $lastId !== false && $bool2 !== false;
            } else {
                $lang = $this->lang->t('newsletter', 'save_success');
                $result = $lastId !== false;
            }

            $this->secureHelper->unsetFormToken($this->request->query);

            if ($result === false) {
                $lang = $this->lang->t('newsletter', 'create_save_error');
            }
            $this->redirectMessages()->setMessage($result, $lang, 'acp/newsletter');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/newsletter');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     * @param array $settings
     */
    private function _editPost(array $formData, array $settings)
    {
        try {
            $validator = $this->get('newsletter.validator');
            $validator->validate($formData);

            // Newsletter archivieren
            $updateValues = [
                'date' => $this->date->toSQL($formData['date']),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'user_id' => $this->auth->getUserId(),
            ];
            $bool = $this->newsletterModel->update($updateValues, $this->request->id);

            // Test-Newsletter
            if ($formData['test'] == 1) {
                $bool2 = $this->newsletterHelpers->sendNewsletter($this->request->id, $settings['mail']);

                $lang = $this->lang->t('newsletter', 'create_success');
                $result = $bool !== false && $bool2;
            } else {
                $lang = $this->lang->t('newsletter', 'save_success');
                $result = $bool !== false;
            }

            $this->secureHelper->unsetFormToken($this->request->query);

            if ($result === false) {
                $lang = $this->lang->t('newsletter', 'create_save_error');
            }

            $this->redirectMessages()->setMessage($result, $lang, 'acp/newsletter');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/newsletter');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     */
    private function _settingsPost(array $formData)
    {
        try {
            $validator = $this->get('newsletter.validator');
            $validator->validateSettings($formData);

            $data = [
                'mail' => $formData['mail'],
                'mailsig' => Core\Functions::strEncode($formData['mailsig'], true),
                'html' => (int)$formData['html']
            ];

            $bool = $this->newsletterConfig->setSettings($data);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/newsletter');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/newsletter');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

}