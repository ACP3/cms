<?php

namespace ACP3\Modules\Guestbook\Controller;

use ACP3\Core;
use ACP3\Modules\Guestbook;

/**
 * Description of GuestbookAdmin
 *
 * @author Tino Goratsch
 */
class Admin extends Core\Modules\Controller\Admin
{
    /**
     * @var Guestbook\Model
     */
    protected $model;

    protected function _init()
    {
        $this->model = new Guestbook\Model($this->db, $this->lang, $this->date, $this->auth);
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/guestbook/delete', 'acp/guestbook');

        if ($this->uri->action === 'confirmed') {
            $items = explode('|', $items);
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->model->delete($item);
            }
            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/guestbook');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionEdit()
    {
        $guestbook = $this->model->getOneById($this->uri->id);
        if (empty($guestbook) === false) {
            $settings = Core\Config::getSettings('guestbook');

            if (empty($_POST) === false) {
                try {
                    $this->model->validateEdit($_POST, $settings);

                    $updateValues = array(
                        'name' => Core\Functions::strEncode($_POST['name']),
                        'message' => Core\Functions::strEncode($_POST['message']),
                        'active' => $settings['notify'] == 2 ? $_POST['active'] : 1,
                    );

                    $bool = $this->model->update($updateValues, $this->uri->id);

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/guestbook');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/guestbook');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

            if ($settings['emoticons'] == 1 && Core\Modules::isActive('emoticons') === true) {
                // Emoticons im Formular anzeigen
                $this->view->assign('emoticons', \ACP3\Modules\Emoticons\Helpers::emoticonsList());
            }

            if ($settings['notify'] == 2) {
                $langActivate = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
                $this->view->assign('activate', Core\Functions::selectGenerator('active', array(1, 0), $langActivate, $guestbook['active'], 'checked'));
            }

            $this->view->assign('form', array_merge($guestbook, $_POST));

            $this->session->generateFormToken();
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionList()
    {
        Core\Functions::getRedirectMessage();

        $guestbook = $this->model->getAllInAcp();
        $c_guestbook = count($guestbook);

        if ($c_guestbook > 0) {
            $can_delete = Core\Modules::hasPermission('guestbook', 'acp_delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $can_delete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $can_delete === true ? 0 : ''
            );
            $this->view->appendContent(Core\Functions::dataTable($config));

            $settings = Core\Config::getSettings('guestbook');
            // Emoticons einbinden
            $emoticons_active = false;
            if ($settings['emoticons'] == 1) {
                if (Core\Modules::isActive('emoticons') === true) {
                    $emoticons_active = true;
                }
            }

            for ($i = 0; $i < $c_guestbook; ++$i) {
                $guestbook[$i]['date_formatted'] = $this->date->formatTimeRange($guestbook[$i]['date']);
                $guestbook[$i]['message'] = Core\Functions::nl2p($guestbook[$i]['message']);
                if ($emoticons_active === true) {
                    $guestbook[$i]['message'] = \ACP3\Modules\Emoticons\Helpers::emoticonsReplace($guestbook[$i]['message']);
                }
            }
            $this->view->assign('guestbook', $guestbook);
            $this->view->assign('can_delete', $can_delete);
        }
    }

    public function actionSettings()
    {
        if (empty($_POST) === false) {
            try {
                $this->model->validateSettings($_POST);

                $data = array(
                    'dateformat' => Core\Functions::strEncode($_POST['dateformat']),
                    'notify' => $_POST['notify'],
                    'notify_email' => $_POST['notify_email'],
                    'overlay' => $_POST['overlay'],
                    'emoticons' => $_POST['emoticons'],
                    'newsletter_integration' => $_POST['newsletter_integration'],
                );
                $bool = Core\Config::setSettings('guestbook', $data);

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/guestbook');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/guestbook');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $settings = Core\Config::getSettings('guestbook');

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
        if (Core\Modules::isActive('emoticons') === true) {
            $lang_allow_emoticons = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('allow_emoticons', Core\Functions::selectGenerator('emoticons', array(1, 0), $lang_allow_emoticons, $settings['emoticons'], 'checked'));
        }

        // In Newsletter integrieren
        if (Core\Modules::isActive('newsletter') === true) {
            $lang_newsletter_integration = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('newsletter_integration', Core\Functions::selectGenerator('newsletter_integration', array(1, 0), $lang_newsletter_integration, $settings['newsletter_integration'], 'checked'));
        }

        $this->view->assign('form', array_merge(array('notify_email' => $settings['notify_email']), $_POST));

        $this->session->generateFormToken();
    }

}