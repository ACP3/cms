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
     * @var \ACP3\Modules\ACP3\Newsletter\Model\NewsletterRepository
     */
    protected $newsletterRepository;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Validator
     */
    protected $newsletterValidator;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\SendNewsletter
     */
    protected $newsletterHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\AccountRepository
     */
    protected $accountRepository;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext               $context
     * @param \ACP3\Core\Date                                          $date
     * @param \ACP3\Core\Helpers\FormToken                             $formTokenHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Model\NewsletterRepository $newsletterRepository
     * @param \ACP3\Modules\ACP3\Newsletter\Model\AccountRepository    $accountRepository
     * @param \ACP3\Modules\ACP3\Newsletter\Validator                  $newsletterValidator
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\SendNewsletter      $newsletterHelpers
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        Newsletter\Model\NewsletterRepository $newsletterRepository,
        Newsletter\Model\AccountRepository $accountRepository,
        Newsletter\Validator $newsletterValidator,
        Newsletter\Helper\SendNewsletter $newsletterHelpers)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->newsletterRepository = $newsletterRepository;
        $this->accountRepository = $accountRepository;
        $this->newsletterValidator = $newsletterValidator;
        $this->newsletterHelpers = $newsletterHelpers;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionCreate()
    {
        $settings = $this->config->getSettings('newsletter');

        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_createPost($this->request->getPost()->all(), $settings);
        }

        $this->view->assign('settings', $settings);
        $this->view->assign('form', array_merge(['title' => '', 'text' => '', 'date' => ''], $this->request->getPost()->all()));

        $this->view->assign('test', $this->get('core.helpers.forms')->yesNoCheckboxGenerator('test', 0));

        $lang_action = [$this->lang->t('newsletter', 'send_and_save'), $this->lang->t('newsletter', 'only_save')];
        $this->view->assign('action', $this->get('core.helpers.forms')->checkboxGenerator('action', [1, 0], $lang_action, 1));

        $this->formTokenHelper->generateFormToken();
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
                    $bool = $this->newsletterRepository->delete($item);
                }

                return $bool;
            }
        );
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        $newsletter = $this->newsletterRepository->getOneById($id);

        if (empty($newsletter) === false) {
            $this->breadcrumb->setTitlePostfix($newsletter['title']);

            $settings = $this->config->getSettings('newsletter');

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->_editPost($this->request->getPost()->all(), $settings, $id);
            }

            $this->view->assign('settings', array_merge($settings, ['html' => $newsletter['html']]));
            $this->view->assign('form', array_merge($newsletter, $this->request->getPost()->all()));

            $this->view->assign('test', $this->get('core.helpers.forms')->yesNoCheckboxGenerator('test', 0));

            $lang_action = [$this->lang->t('newsletter', 'send_and_save'), $this->lang->t('newsletter', 'only_save')];
            $this->view->assign('action', $this->get('core.helpers.forms')->checkboxGenerator('action', [1, 0], $lang_action, 1));

            $this->formTokenHelper->generateFormToken();
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $newsletter = $this->newsletterRepository->getAllInAcp();

        if (count($newsletter) > 0) {
            $canDelete = $this->acl->hasPermission('admin/newsletter/index/delete');
            $config = [
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->user->getEntriesPerPage()
            ];
            $this->view->assign('datatable_config', $config);
            $this->view->assign('newsletter', $newsletter);
            $this->view->assign('can_delete', $canDelete);
            $this->view->assign('can_send', $this->acl->hasPermission('admin/newsletter/index/send'));
            $this->view->assign('has_active_newsletter_accounts', $this->accountRepository->countAllActiveAccounts() > 0);
        }
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionSend($id)
    {
        if ($this->newsletterRepository->newsletterExists($id) === true) {
            $accounts = $this->accountRepository->getAllActiveAccounts();
            $c_accounts = count($accounts);
            $recipients = [];

            for ($i = 0; $i < $c_accounts; ++$i) {
                $recipients[] = $accounts[$i]['mail'];
            }

            $bool = $this->newsletterHelpers->sendNewsletter($id, $recipients);
            $bool2 = false;
            if ($bool === true) {
                $bool2 = $this->newsletterRepository->update(['status' => '1'], $id);
            }

            return $this->redirectMessages()->setMessage($bool && $bool2 !== false, $this->lang->t('newsletter', $bool === true && $bool2 !== false ? 'create_success' : 'create_save_error'));
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionSettings()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->_settingsPost($this->request->getPost()->all());
        }

        $settings = $this->config->getSettings('newsletter');

        $this->view->assign('form', array_merge($settings, $this->request->getPost()->all()));

        $this->view->assign('html', $this->get('core.helpers.forms')->yesNoCheckboxGenerator('html', $settings['html']));

        $this->formTokenHelper->generateFormToken();
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _createPost(array $formData, array $settings)
    {
        return $this->actionHelper->handlePostAction(function () use ($formData, $settings) {
            $this->newsletterValidator->validate($formData);

            // Newsletter archivieren
            $insertValues = [
                'id' => '',
                'date' => $this->date->toSQL($formData['date']),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'html' => $settings['html'],
                'status' => 0,
                'user_id' => $this->user->getUserId(),
            ];
            $lastId = $this->newsletterRepository->insert($insertValues);

            // Test-Newsletter
            if ($formData['test'] == 1) {
                $bool2 = $this->newsletterHelpers->sendNewsletter($lastId, $settings['mail']);

                $lang = $this->lang->t('newsletter', 'create_success');
                $result = $lastId !== false && $bool2 !== false;
            } else {
                $lang = $this->lang->t('newsletter', 'save_success');
                $result = $lastId !== false;
            }

            $this->formTokenHelper->unsetFormToken();

            if ($result === false) {
                $lang = $this->lang->t('newsletter', 'create_save_error');
            }

            return $this->redirectMessages()->setMessage($result, $lang);
        });
    }

    /**
     * @param array $formData
     * @param array $settings
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _editPost(array $formData, array $settings, $id)
    {
        return $this->actionHelper->handlePostAction(function () use ($formData, $settings, $id) {
            $this->newsletterValidator->validate($formData);

            // Newsletter archivieren
            $updateValues = [
                'date' => $this->date->toSQL($formData['date']),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'user_id' => $this->user->getUserId(),
            ];
            $bool = $this->newsletterRepository->update($updateValues, $id);

            // Test-Newsletter
            if ($formData['test'] == 1) {
                $bool2 = $this->newsletterHelpers->sendNewsletter($id, $settings['mail']);

                $lang = $this->lang->t('newsletter', 'create_success');
                $result = $bool !== false && $bool2;
            } else {
                $lang = $this->lang->t('newsletter', 'save_success');
                $result = $bool !== false;
            }

            $this->formTokenHelper->unsetFormToken();

            if ($result === false) {
                $lang = $this->lang->t('newsletter', 'create_save_error');
            }

            return $this->redirectMessages()->setMessage($result, $lang);
        });
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function _settingsPost(array $formData)
    {
        return $this->actionHelper->handleSettingsPostAction(function () use ($formData) {
            $this->newsletterValidator->validateSettings($formData);

            $data = [
                'mail' => $formData['mail'],
                'mailsig' => Core\Functions::strEncode($formData['mailsig'], true),
                'html' => (int)$formData['html']
            ];

            $this->formTokenHelper->unsetFormToken();

            return $this->config->setSettings($data, 'newsletter');
        });
    }
}
