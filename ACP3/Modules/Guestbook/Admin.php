<?php

namespace ACP3\Modules\Guestbook;

use ACP3\Core;

/**
 * Description of GuestbookAdmin
 *
 * @author Tino Goratsch
 */
class Admin extends Core\Modules\Controller\Admin
{

    public function __construct()
    {
        parent::__construct();
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/guestbook/delete', 'acp/guestbook');

        if ($this->uri->action === 'confirmed') {
            $items = explode('|', $items);
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->db->delete(DB_PRE . 'guestbook', array('id' => $item));
            }
            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/guestbook');
        } elseif (is_string($items)) {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionEdit()
    {
        if (Core\Validate::isNumber($this->uri->id) === true &&
            $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'guestbook WHERE id = ?', array($this->uri->id)) == 1
        ) {
            $settings = Core\Config::getSettings('guestbook');

            if (isset($_POST['submit']) === true) {
                if (empty($_POST['name']))
                    $errors['name'] = $this->lang->t('system', 'name_to_short');
                if (strlen($_POST['message']) < 3)
                    $errors['message'] = $this->lang->t('system', 'message_to_short');
                if ($settings['notify'] == 2 && (!isset($_POST['active']) || ($_POST['active'] != 0 && $_POST['active'] != 1)))
                    $errors['notify'] = $this->lang->t('guestbook', 'select_activate');

                if (isset($errors) === true) {
                    $this->view->assign('error_msg', Core\Functions::errorBox($errors));
                } elseif (Core\Validate::formToken() === false) {
                    $this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
                } else {
                    $update_values = array(
                        'name' => Core\Functions::strEncode($_POST['name']),
                        'message' => Core\Functions::strEncode($_POST['message']),
                        'active' => $settings['notify'] == 2 ? $_POST['active'] : 1,
                    );

                    $bool = $this->db->update(DB_PRE . 'guestbook', $update_values, array('id' => $this->uri->id));

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/guestbook');
                }
            }
            if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
                $guestbook = $this->db->fetchAssoc('SELECT name, message, active FROM ' . DB_PRE . 'guestbook WHERE id = ?', array($this->uri->id));

                if ($settings['emoticons'] == 1 && Core\Modules::isActive('emoticons') === true) {
                    //Emoticons im Formular anzeigen
                    $this->view->assign('emoticons', \ACP3\Modules\Emoticons\Helpers::emoticonsList());
                }

                if ($settings['notify'] == 2) {
                    $activate = array();
                    $activate[0]['value'] = '1';
                    $activate[0]['checked'] = Core\Functions::selectEntry('active', '1', $guestbook['active'], 'checked');
                    $activate[0]['lang'] = $this->lang->t('system', 'yes');
                    $activate[1]['value'] = '0';
                    $activate[1]['checked'] = Core\Functions::selectEntry('active', '0', $guestbook['active'], 'checked');
                    $activate[1]['lang'] = $this->lang->t('system', 'no');
                    $this->view->assign('activate', $activate);
                }

                $this->view->assign('form', isset($_POST['submit']) ? $_POST : $guestbook);

                $this->session->generateFormToken();
            }
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionList()
    {
        Core\Functions::getRedirectMessage();

        $guestbook = $this->db->fetchAll('SELECT id, ip, date, name, message FROM ' . DB_PRE . 'guestbook ORDER BY date DESC');
        $c_guestbook = count($guestbook);

        if ($c_guestbook > 0) {
            $can_delete = Core\Modules::hasPermission('guestbook', 'acp_delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $can_delete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $can_delete === true ? 0 : ''
            );
            $this->view->appendContent(Core\Functions::datatable($config));

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
        $emoticons_active = Core\Modules::isActive('emoticons');
        $newsletter_active = Core\Modules::isActive('newsletter');

        if (isset($_POST['submit']) === true) {
            if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
                $errors['dateformat'] = $this->lang->t('system', 'select_date_format');
            if (!isset($_POST['notify']) || ($_POST['notify'] != 0 && $_POST['notify'] != 1 && $_POST['notify'] != 2))
                $errors['notify'] = $this->lang->t('guestbook', 'select_notification_type');
            if ($_POST['notify'] != 0 && Core\Validate::email($_POST['notify_email']) === false)
                $errors['notify-email'] = $this->lang->t('system', 'wrong_email_format');
            if (!isset($_POST['overlay']) || $_POST['overlay'] != 1 && $_POST['overlay'] != 0)
                $errors[] = $this->lang->t('guestbook', 'select_use_overlay');
            if ($emoticons_active === true && (!isset($_POST['emoticons']) || ($_POST['emoticons'] != 0 && $_POST['emoticons'] != 1)))
                $errors[] = $this->lang->t('guestbook', 'select_emoticons');
            if ($newsletter_active === true && (!isset($_POST['newsletter_integration']) || ($_POST['newsletter_integration'] != 0 && $_POST['newsletter_integration'] != 1)))
                $errors[] = $this->lang->t('guestbook', 'select_newsletter_integration');

            if (isset($errors) === true) {
                $this->view->assign('error_msg', Core\Functions::errorBox($errors));
            } elseif (Core\Validate::formToken() === false) {
                $this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
            } else {
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
            }
        }
        if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
            $settings = Core\Config::getSettings('guestbook');

            $this->view->assign('dateformat', $this->date->dateformatDropdown($settings['dateformat']));

            $lang_notify = array(
                $this->lang->t('guestbook', 'no_notification'),
                $this->lang->t('guestbook', 'notify_on_new_entry'),
                $this->lang->t('guestbook', 'notify_and_enable')
            );
            $this->view->assign('notify', Core\Functions::selectGenerator('notify', array(0, 1, 2), $lang_notify, $settings['notify']));

            $lang_overlay = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('overlay', Core\Functions::selectGenerator('overlay', array(1, 0), $lang_overlay, $settings['overlay'], 'checked'));

            // Emoticons erlauben
            if ($emoticons_active === true) {
                $lang_allow_emoticons = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
                $this->view->assign('allow_emoticons', Core\Functions::selectGenerator('emoticons', array(1, 0), $lang_allow_emoticons, $settings['emoticons'], 'checked'));
            }

            // In Newsletter integrieren
            if ($newsletter_active === true) {
                $lang_newsletter_integration = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
                $this->view->assign('newsletter_integration', Core\Functions::selectGenerator('newsletter_integration', array(1, 0), $lang_newsletter_integration, $settings['newsletter_integration'], 'checked'));
            }

            $this->view->assign('form', isset($_POST['submit']) ? $_POST : array('notify_email' => $settings['notify_email']));

            $this->session->generateFormToken();
        }
    }

}