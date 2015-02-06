<?php

namespace ACP3\Modules\Guestbook\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Emoticons;
use ACP3\Modules\Guestbook;

/**
 * Class Index
 * @package ACP3\Modules\Guestbook\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\Guestbook\Model
     */
    protected $guestbookModel;
    /**
     * @var \ACP3\Modules\Guestbook\Validator
     */
    protected $guestbookValidator;
    /**
     * @var \ACP3\Modules\Emoticons\Helpers
     */
    protected $emoticonsHelpers;

    /**
     * @param \ACP3\Core\Context\Admin          $context
     * @param \ACP3\Core\Date                   $date
     * @param \ACP3\Core\Helpers\Secure         $secureHelper
     * @param \ACP3\Modules\Guestbook\Model     $guestbookModel
     * @param \ACP3\Modules\Guestbook\Validator $guestbookValidator
     */
    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        Core\Helpers\Secure $secureHelper,
        Guestbook\Model $guestbookModel,
        Guestbook\Validator $guestbookValidator)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->secureHelper = $secureHelper;
        $this->guestbookModel = $guestbookModel;
        $this->guestbookValidator = $guestbookValidator;
    }

    /**
     * @param \ACP3\Modules\Emoticons\Helpers $emoticonsHelpers
     *
     * @return $this
     */
    public function setEmoticonsHelpers(Emoticons\Helpers $emoticonsHelpers)
    {
        $this->emoticonsHelpers = $emoticonsHelpers;

        return $this;
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem();

        if ($this->request->action === 'confirmed') {
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->guestbookModel->delete($item);
            }

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'));
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $guestbook = $this->guestbookModel->getOneById($this->request->id);
        if (empty($guestbook) === false) {
            $settings = $this->config->getSettings('guestbook');

            $this->breadcrumb->setTitlePostfix($guestbook['name']);

            if (empty($_POST) === false) {
                $this->_editPost($_POST, $settings);
            }

            if ($settings['emoticons'] == 1 && $this->emoticonsHelpers) {
                // Emoticons im Formular anzeigen
                $this->view->assign('emoticons', $this->emoticonsHelpers->emoticonsList());
            }

            if ($settings['notify'] == 2) {
                $langActivate = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
                $this->view->assign('activate', $this->get('core.helpers.forms')->selectGenerator('active', [1, 0], $langActivate, $guestbook['active'], 'checked'));
            }

            $this->view->assign('form', array_merge($guestbook, $_POST));

            $this->secureHelper->generateFormToken($this->request->query);
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
        if (empty($_POST) === false) {
            $this->_settingsPost($_POST);
        }

        $settings = $this->config->getSettings('guestbook');

        $this->view->assign('dateformat', $this->date->dateFormatDropdown($settings['dateformat']));

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

        $this->view->assign('form', array_merge(['notify_email' => $settings['notify_email']], $_POST));

        $this->secureHelper->generateFormToken($this->request->query);
    }

    /**
     * @param array $formData
     * @param array $settings
     */
    private function _editPost(array $formData, array $settings)
    {
        try {
            $this->guestbookValidator->validateEdit($formData, $settings);

            $updateValues = [
                'name' => Core\Functions::strEncode($formData['name']),
                'message' => Core\Functions::strEncode($formData['message']),
                'active' => $settings['notify'] == 2 ? $formData['active'] : 1,
            ];

            $bool = $this->guestbookModel->update($updateValues, $this->request->id);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
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
            $this->guestbookValidator->validateSettings($formData);

            $data = [
                'dateformat' => Core\Functions::strEncode($formData['dateformat']),
                'notify' => $formData['notify'],
                'notify_email' => $formData['notify_email'],
                'overlay' => $formData['overlay'],
                'emoticons' => $formData['emoticons'],
                'newsletter_integration' => $formData['newsletter_integration'],
            ];
            $bool = $this->config->setSettings($data, 'guestbook');

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
