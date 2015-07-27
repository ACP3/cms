<?php

namespace ACP3\Modules\ACP3\Guestbook\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons;
use ACP3\Modules\ACP3\Guestbook;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Guestbook\Controller\Admin
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
     * @var \ACP3\Modules\ACP3\Guestbook\Model
     */
    protected $guestbookModel;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Validator
     */
    protected $guestbookValidator;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Helpers
     */
    protected $emoticonsHelpers;

    /**
     * @param \ACP3\Core\Modules\Controller\AdminContext $context
     * @param \ACP3\Core\Date                            $date
     * @param \ACP3\Core\Helpers\FormToken               $formTokenHelper
     * @param \ACP3\Modules\ACP3\Guestbook\Model         $guestbookModel
     * @param \ACP3\Modules\ACP3\Guestbook\Validator     $guestbookValidator
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        Guestbook\Model $guestbookModel,
        Guestbook\Validator $guestbookValidator)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->guestbookModel = $guestbookModel;
        $this->guestbookValidator = $guestbookValidator;
    }

    /**
     * @param \ACP3\Modules\ACP3\Emoticons\Helpers $emoticonsHelpers
     *
     * @return $this
     */
    public function setEmoticonsHelpers(Emoticons\Helpers $emoticonsHelpers)
    {
        $this->emoticonsHelpers = $emoticonsHelpers;

        return $this;
    }

    /**
     * @param string $action
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDelete($action = '')
    {
        $this->handleDeleteAction(
            $action,
            function ($items) {
                $bool = false;
                foreach ($items as $item) {
                    $bool = $this->guestbookModel->delete($item);
                }

                return $bool;
            }
        );
    }

    /**
     * @param int $id
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionEdit($id)
    {
        $guestbook = $this->guestbookModel->getOneById($id);
        if (empty($guestbook) === false) {
            $settings = $this->config->getSettings('guestbook');

            $this->breadcrumb->setTitlePostfix($guestbook['name']);

            if ($this->request->getPost()->isEmpty() === false) {
                $this->_editPost($this->request->getPost()->getAll(), $settings, $id);
            }

            if ($settings['emoticons'] == 1 && $this->emoticonsHelpers) {
                // Emoticons im Formular anzeigen
                $this->view->assign('emoticons', $this->emoticonsHelpers->emoticonsList());
            }

            if ($settings['notify'] == 2) {
                $langActivate = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
                $this->view->assign('activate', $this->get('core.helpers.forms')->selectGenerator('active', [1, 0], $langActivate, $guestbook['active'], 'checked'));
            }

            $this->view->assign('form', array_merge($guestbook, $this->request->getPost()->getAll()));

            $this->formTokenHelper->generateFormToken($this->request->getQuery());
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $guestbook = $this->guestbookModel->getAllInAcp();
        $c_guestbook = count($guestbook);

        if ($c_guestbook > 0) {
            $canDelete = $this->acl->hasPermission('admin/guestbook/index/delete');
            $config = [
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : '',
                'records_per_page' => $this->auth->entries
            ];
            $this->view->assign('datatable_config', $config);

            $settings = $this->config->getSettings('guestbook');

            // Emoticons einbinden
            $emoticonsActive = ($settings['emoticons'] == 1 && $this->modules->isActive('emoticons') === true);

            for ($i = 0; $i < $c_guestbook; ++$i) {
                if ($emoticonsActive === true && $this->emoticonsHelpers) {
                    $guestbook[$i]['message'] = $this->emoticonsHelpers->emoticonsReplace($guestbook[$i]['message']);
                }
            }
            $this->view->assign('guestbook', $guestbook);
            $this->view->assign('can_delete', $canDelete);
        }
    }

    public function actionSettings()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_settingsPost($this->request->getPost()->getAll());
        }

        $settings = $this->config->getSettings('guestbook');

        $this->view->assign('dateformat', $this->get('core.helpers.date')->dateFormatDropdown($settings['dateformat']));

        $lang_notify = [
            $this->lang->t('guestbook', 'no_notification'),
            $this->lang->t('guestbook', 'notify_on_new_entry'),
            $this->lang->t('guestbook', 'notify_and_enable')
        ];
        $this->view->assign('notify', $this->get('core.helpers.forms')->selectGenerator('notify', [0, 1, 2], $lang_notify, $settings['notify']));

        $lang_overlay = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('overlay', $this->get('core.helpers.forms')->selectGenerator('overlay', [1, 0], $lang_overlay, $settings['overlay'], 'checked'));

        // Emoticons erlauben
        if ($this->modules->isActive('emoticons') === true) {
            $lang_allow_emoticons = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
            $this->view->assign('allow_emoticons', $this->get('core.helpers.forms')->selectGenerator('emoticons', [1, 0], $lang_allow_emoticons, $settings['emoticons'], 'checked'));
        }

        // In Newsletter integrieren
        if ($this->modules->isActive('newsletter') === true) {
            $lang_newsletter_integration = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
            $this->view->assign('newsletter_integration', $this->get('core.helpers.forms')->selectGenerator('newsletter_integration', [1, 0], $lang_newsletter_integration, $settings['newsletter_integration'], 'checked'));
        }

        $this->view->assign('form', array_merge(['notify_email' => $settings['notify_email']], $this->request->getPost()->getAll()));

        $this->formTokenHelper->generateFormToken($this->request->getQuery());
    }

    /**
     * @param array $formData
     * @param array $settings
     * @param int   $id
     */
    protected function _editPost(array $formData, array $settings, $id)
    {
        $this->handleEditPostAction(function () use ($formData, $settings, $id) {
            $this->guestbookValidator->validateEdit($formData, $settings);

            $updateValues = [
                'name' => Core\Functions::strEncode($formData['name']),
                'message' => Core\Functions::strEncode($formData['message']),
                'active' => $settings['notify'] == 2 ? $formData['active'] : 1,
            ];

            $bool = $this->guestbookModel->update($updateValues, $id);

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            return $bool;
        });
    }

    /**
     * @param array $formData
     */
    protected function _settingsPost(array $formData)
    {
        $this->handleSettingsPostAction(function () use ($formData) {
            $this->guestbookValidator->validateSettings($formData);

            $data = [
                'dateformat' => Core\Functions::strEncode($formData['dateformat']),
                'notify' => $formData['notify'],
                'notify_email' => $formData['notify_email'],
                'overlay' => $formData['overlay'],
                'emoticons' => $formData['emoticons'],
                'newsletter_integration' => $formData['newsletter_integration'],
            ];

            $this->formTokenHelper->unsetFormToken($this->request->getQuery());

            return $this->config->setSettings($data, 'guestbook');
        });
    }
}
