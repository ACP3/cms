<?php

namespace ACP3\Modules\Guestbook\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Guestbook;

/**
 * Class Index
 * @package ACP3\Modules\Guestbook\Controller\Admin
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
     * @var Guestbook\Model
     */
    protected $guestbookModel;
    /**
     * @var Core\Config
     */
    protected $guestbookConfig;

    public function __construct(
        Core\Context\Admin $context,
        Core\Date $date,
        Core\Helpers\Secure $secureHelper,
        Guestbook\Model $guestbookModel,
        Core\Config $guestbookConfig)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->secureHelper = $secureHelper;
        $this->guestbookModel = $guestbookModel;
        $this->guestbookConfig = $guestbookConfig;
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/guestbook/index/delete', 'acp/guestbook');

        if ($this->request->action === 'confirmed') {
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->guestbookModel->delete($item);
            }

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/guestbook');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionEdit()
    {
        $guestbook = $this->guestbookModel->getOneById($this->request->id);
        if (empty($guestbook) === false) {
            $settings = $this->guestbookConfig->getSettings();

            if (empty($_POST) === false) {
                $this->_editPost($_POST, $settings);
            }

            if ($settings['emoticons'] == 1 && $this->modules->isActive('emoticons') === true) {
                // Emoticons im Formular anzeigen
                $this->view->assign('emoticons', $this->get('emoticons.helpers')->emoticonsList());
            }

            if ($settings['notify'] == 2) {
                $langActivate = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
                $this->view->assign('activate', Core\Functions::selectGenerator('active', array(1, 0), $langActivate, $guestbook['active'], 'checked'));
            }

            $this->view->assign('form', array_merge($guestbook, $_POST));

            $this->secureHelper->generateFormToken($this->request->query);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $this->redirectMessages()->getMessage();

        $guestbook = $this->guestbookModel->getAllInAcp();
        $c_guestbook = count($guestbook);

        if ($c_guestbook > 0) {
            $canDelete = $this->modules->hasPermission('admin/guestbook/index/delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : ''
            );
            $this->appendContent($this->get('core.functions')->dataTable($config));

            $settings = $this->guestbookConfig->getSettings();
            // Emoticons einbinden
            $emoticons_active = false;
            if ($settings['emoticons'] == 1) {
                if ($this->modules->isActive('emoticons') === true) {
                    $emoticons_active = true;
                }
            }

            $formatter = $this->get('core.helpers.stringFormatter');
            for ($i = 0; $i < $c_guestbook; ++$i) {
                $guestbook[$i]['date_formatted'] = $this->date->formatTimeRange($guestbook[$i]['date']);
                $guestbook[$i]['message'] = $formatter->nl2p($guestbook[$i]['message']);
                if ($emoticons_active === true) {
                    $guestbook[$i]['message'] = $this->get('emoticons.helpers')->emoticonsReplace($guestbook[$i]['message']);
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

        $settings = $this->guestbookConfig->getSettings();

        $this->view->assign('dateformat', $this->date->dateFormatDropdown($settings['dateformat']));

        $lang_notify = array(
            $this->lang->t('guestbook', 'no_notification'),
            $this->lang->t('guestbook', 'notify_on_new_entry'),
            $this->lang->t('guestbook', 'notify_and_enable')
        );
        $this->view->assign('notify', Core\Functions::selectGenerator('notify', array(0, 1, 2), $lang_notify, $settings['notify']));

        $lang_overlay = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
        $this->view->assign('overlay', Core\Functions::selectGenerator('overlay', array(1, 0), $lang_overlay, $settings['overlay'], 'checked'));

        // Emoticons erlauben
        if ($this->modules->isActive('emoticons') === true) {
            $lang_allow_emoticons = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('allow_emoticons', Core\Functions::selectGenerator('emoticons', array(1, 0), $lang_allow_emoticons, $settings['emoticons'], 'checked'));
        }

        // In Newsletter integrieren
        if ($this->modules->isActive('newsletter') === true) {
            $lang_newsletter_integration = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('newsletter_integration', Core\Functions::selectGenerator('newsletter_integration', array(1, 0), $lang_newsletter_integration, $settings['newsletter_integration'], 'checked'));
        }

        $this->view->assign('form', array_merge(array('notify_email' => $settings['notify_email']), $_POST));

        $this->secureHelper->generateFormToken($this->request->query);
    }

    private function _editPost(array $formData, array $settings)
    {
        try {
            $validator = $this->get('guestbook.validator');
            $validator->validateEdit($formData, $settings);

            $updateValues = array(
                'name' => Core\Functions::strEncode($formData['name']),
                'message' => Core\Functions::strEncode($formData['message']),
                'active' => $settings['notify'] == 2 ? $formData['active'] : 1,
            );

            $bool = $this->guestbookModel->update($updateValues, $this->request->id);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/guestbook');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/guestbook');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    private function _settingsPost(array $formData)
    {
        try {
            $validator = $this->get('guestbook.validator');
            $validator->validateSettings($formData);

            $data = array(
                'dateformat' => Core\Functions::strEncode($formData['dateformat']),
                'notify' => $formData['notify'],
                'notify_email' => $formData['notify_email'],
                'overlay' => $formData['overlay'],
                'emoticons' => $formData['emoticons'],
                'newsletter_integration' => $formData['newsletter_integration'],
            );
            $bool = $this->guestbookConfig->setSettings($data);

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/guestbook');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/guestbook');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

}