<?php

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Newsletter\Controller\Admin
 */
class Index extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model
     */
    protected $newsletterModel;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Validator
     */
    protected $newsletterValidator;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helpers
     */
    protected $newsletterHelpers;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param \ACP3\Core\Date                            $date
     * @param \ACP3\Core\Helpers\FormToken               $formTokenHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Model        $newsletterModel
     * @param \ACP3\Modules\ACP3\Newsletter\Validator    $newsletterValidator
     * @param \ACP3\Modules\ACP3\Newsletter\Helpers      $newsletterHelpers
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        Newsletter\Model $newsletterModel,
        Newsletter\Validator $newsletterValidator,
        Newsletter\Helpers $newsletterHelpers)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->newsletterModel = $newsletterModel;
        $this->newsletterValidator = $newsletterValidator;
        $this->newsletterHelpers = $newsletterHelpers;
    }

    public function actionCreate()
    {
        $settings = $this->config->getSettings('newsletter');

        if ($this->request->getPost()->isEmpty() === false) {
            $this->_createPost($this->request->getPost()->getAll(), $settings);
        }

        $this->view->assign('date', $this->get('core.helpers.date')->datepicker('date'));
        $this->view->assign('settings', $settings);
        $this->view->assign('form', array_merge(['title' => '', 'text' => ''], $this->request->getPost()->getAll()));

        $lang_test = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('test', $this->get('core.helpers.forms')->selectGenerator('test', [1, 0], $lang_test, 0, 'checked'));

        $lang_action = [$this->lang->t('newsletter', 'send_and_save'), $this->lang->t('newsletter', 'only_save')];
        $this->view->assign('action', $this->get('core.helpers.forms')->selectGenerator('action', [1, 0], $lang_action, 1, 'checked'));

        $this->formTokenHelper->generateFormToken($this->request->getQuery());
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem();

        if ($this->request->getParameters()->get('action') === 'confirmed') {
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->newsletterModel->delete($item);
            }

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'));
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $newsletter = $this->newsletterModel->getOneById($this->request->getParameters()->get('id'));

        if (empty($newsletter) === false) {
            $this->breadcrumb->setTitlePostfix($newsletter['title']);

            $settings = $this->config->getSettings('newsletter');

            if ($this->request->getPost()->isEmpty() === false) {
                $this->_editPost($this->request->getPost()->getAll(), $settings);
            }

            $this->view->assign('date', $this->get('core.helpers.date')->datepicker('date', $newsletter['date']));
            $this->view->assign('settings', array_merge($settings, ['html' => $newsletter['html']]));
            $this->view->assign('form', array_merge($newsletter, $this->request->getPost()->getAll()));

            $lang_test = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
            $this->view->assign('test', $this->get('core.helpers.forms')->selectGenerator('test', [1, 0], $lang_test, 0, 'checked'));

            $lang_action = [$this->lang->t('newsletter', 'send_and_save'), $this->lang->t('newsletter', 'only_save')];
            $this->view->assign('action', $this->get('core.helpers.forms')->selectGenerator('action', [1, 0], $lang_action, 1, 'checked'));

            $this->formTokenHelper->generateFormToken($this->request->getQuery());
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
        if ($this->get('core.validator.rules.misc')->isNumber($this->request->getParameters()->get('id')) === true &&
            $this->newsletterModel->newsletterExists($this->request->getParameters()->get('id')) === true
        ) {
            $accounts = $this->newsletterModel->getAllActiveAccounts();
            $c_accounts = count($accounts);
            $recipients = [];

            for ($i = 0; $i < $c_accounts; ++$i) {
                $recipients[] = $accounts[$i]['mail'];
            }

            $bool = $this->newsletterHelpers->sendNewsletter($this->request->getParameters()->get('id'), $recipients);
            $bool2 = false;
            if ($bool === true) {
                $bool2 = $this->newsletterModel->update(['status' => '1'], $this->request->getParameters()->get('id'));
            }

            $this->redirectMessages()->setMessage($bool && $bool2 !== false, $this->lang->t('newsletter', $bool === true && $bool2 !== false ? 'create_success' : 'create_save_error'));
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionSettings()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_settingsPost($this->request->getPost()->getAll());
        }

        $settings = $this->config->getSettings('newsletter');

        $this->view->assign('form', array_merge($settings, $this->request->getPost()->getAll()));

        $langHtml = [
            $this->lang->t('system', 'yes'),
            $this->lang->t('system', 'no')
        ];
        $this->view->assign('html', $this->get('core.helpers.forms')->selectGenerator('html', [1, 0], $langHtml, $settings['html'], 'checked'));

        $this->formTokenHelper->generateFormToken($this->request->getQuery());
    }

    /**
     * @param array $formData
     * @param array $settings
     */
    protected function _createPost(array $formData, array $settings)
    {
        try {
            $this->newsletterValidator->validate($formData);

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

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            if ($result === false) {
                $lang = $this->lang->t('newsletter', 'create_save_error');
            }
            $this->redirectMessages()->setMessage($result, $lang);
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @param array $formData
     * @param array $settings
     */
    protected function _editPost(array $formData, array $settings)
    {
        try {
            $this->newsletterValidator->validate($formData);

            // Newsletter archivieren
            $updateValues = [
                'date' => $this->date->toSQL($formData['date']),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'user_id' => $this->auth->getUserId(),
            ];
            $bool = $this->newsletterModel->update($updateValues, $this->request->getParameters()->get('id'));

            // Test-Newsletter
            if ($formData['test'] == 1) {
                $bool2 = $this->newsletterHelpers->sendNewsletter($this->request->getParameters()->get('id'), $settings['mail']);

                $lang = $this->lang->t('newsletter', 'create_success');
                $result = $bool !== false && $bool2;
            } else {
                $lang = $this->lang->t('newsletter', 'save_success');
                $result = $bool !== false;
            }

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

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
    protected function _settingsPost(array $formData)
    {
        try {
            $this->newsletterValidator->validateSettings($formData);

            $data = [
                'mail' => $formData['mail'],
                'mailsig' => Core\Functions::strEncode($formData['mailsig'], true),
                'html' => (int)$formData['html']
            ];

            $bool = $this->config->setSettings($data, 'newsletter');

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
