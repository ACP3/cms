<?php

namespace ACP3\Modules\Guestbook\Controller;

use ACP3\Core;
use ACP3\Modules\Guestbook;

/**
 * Description of GuestbookFrontend
 *
 * @author Tino Goratsch
 */
class Frontend extends Core\Modules\Controller
{
    /**
     *
     * @var Model
     */
    protected $model;

    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        Core\Lang $lang,
        Core\Session $session,
        Core\URI $uri,
        Core\View $view)
    {
        parent::__construct($auth, $breadcrumb, $date, $db, $lang, $session, $uri, $view);

        $this->model = new Guestbook\Model($this->db);
    }

    public function actionCreate()
    {
        $this->breadcrumb
            ->append($this->lang->t('guestbook', 'guestbook'), $this->uri->route('guestbook'))
            ->append($this->lang->t('guestbook', 'create'));

        $settings = Core\Config::getSettings('guestbook');
        $hasNewsletterAccess = Core\Modules::hasPermission('newsletter', 'list') === true && $settings['newsletter_integration'] == 1;

        $overlayIsActive = false;
        if ($this->uri->layout === 'simple') {
            $overlayIsActive = true;
            $this->view->setLayout('simple.tpl');
        }

        if (isset($_POST['submit']) === true) {
            try {
                $this->model->validateCreate($_POST, $hasNewsletterAccess, $this->lang, $this->date, $this->auth);

                $insertValues = array(
                    'id' => '',
                    'date' => $this->date->toSQL(),
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'name' => Core\Functions::strEncode($_POST['name']),
                    'user_id' => $this->auth->isUser() ? $this->auth->getUserId() : '',
                    'message' => Core\Functions::strEncode($_POST['message']),
                    'website' => Core\Functions::strEncode($_POST['website']),
                    'mail' => $_POST['mail'],
                    'active' => $settings['notify'] == 2 ? 0 : 1,
                );

                $lastId = $this->model->insert($insertValues);

                // E-Mail-Benachrichtigung bei neuem Eintrag der hinterlegten
                // E-Mail-Adresse zusenden
                if ($settings['notify'] == 1 || $settings['notify'] == 2) {
                    $host = 'http://' . htmlentities($_SERVER['HTTP_HOST']);
                    $fullPath = $host . $this->uri->route('guestbook/list') . '#gb-entry-' . $this->db->lastInsertId();
                    $body = sprintf($settings['notify'] == 1 ? $this->lang->t('guestbook', 'notification_email_body_1') : $this->lang->t('guestbook', 'notification_email_body_2'), $host, $fullPath);
                    Core\Functions::generateEmail('', $settings['notify_email'], $settings['notify_email'], $this->lang->t('guestbook', 'notification_email_subject'), $body);
                }

                // Falls es der Benutzer ausgewählt hat, diesen in den Newsletter eintragen
                if ($hasNewsletterAccess === true && isset($_POST['subscribe_newsletter']) && $_POST['subscribe_newsletter'] == 1) {
                    \ACP3\Modules\Newsletter\Helpers::subscribeToNewsletter($_POST['mail']);
                }

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'), 'guestbook', (bool)$overlayIsActive);
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'guestbook');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        // Emoticons einbinden
        if ($settings['emoticons'] == 1 && Core\Modules::isActive('emoticons') === true) {
            $this->view->assign('emoticons', \ACP3\Modules\Emoticons\Helpers::emoticonsList());
        }

        // In Newsletter integrieren
        if ($hasNewsletterAccess === true) {
            $this->view->assign('subscribe_newsletter', Core\Functions::selectEntry('subscribe_newsletter', '1', '1', 'checked'));
            $this->view->assign('LANG_subscribe_to_newsletter', sprintf($this->lang->t('guestbook', 'subscribe_to_newsletter'), CONFIG_SEO_TITLE));
        }

        // Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
        if ($this->auth->isUser() === true) {
            $user = $this->auth->getUserInfo();
            $disabled = ' readonly="readonly" class="readonly"';

            if (isset($_POST['submit'])) {
                $_POST['name'] = $user['nickname'];
                $_POST['name_disabled'] = $disabled;
                $_POST['mail'] = $user['mail'];
                $_POST['mail_disabled'] = $disabled;
                $_POST['website_disabled'] = !empty($user['website']) ? $disabled : '';
            } else {
                $user['name'] = $user['nickname'];
                $user['name_disabled'] = $disabled;
                $user['mail_disabled'] = $disabled;
                $user['website_disabled'] = !empty($user['website']) ? $disabled : '';
                $user['message'] = '';
            }
            $this->view->assign('form', isset($_POST['submit']) ? $_POST : $user);
        } else {
            $defaults = array(
                'name' => '',
                'name_disabled' => '',
                'mail' => '',
                'mail_disabled' => '',
                'website' => '',
                'website_disabled' => '',
                'message' => '',
            );

            $this->view->assign('form', isset($_POST['submit']) ? array_merge($defaults, $_POST) : $defaults);
        }

        if (Core\Modules::hasPermission('captcha', 'image') === true) {
            $this->view->assign('captcha', \ACP3\Modules\Captcha\Helpers::captcha());
        }

        $this->session->generateFormToken();
    }

    public function actionList()
    {
        Core\Functions::getRedirectMessage();

        $settings = Core\Config::getSettings('guestbook');
        $this->view->assign('overlay', $settings['overlay']);

        $guestbook = $this->model->getAll($settings['notify'], POS, $this->auth->entries);
        $c_guestbook = count($guestbook);

        if ($c_guestbook > 0) {
            $this->view->assign('pagination', Core\Functions::pagination($this->model->countAll($settings['notify'])));

            // Emoticons einbinden
            $emoticons_active = false;
            if ($settings['emoticons'] == 1) {
                $emoticons_active = Core\Modules::isActive('emoticons') === true && $settings['emoticons'] == 1 ? true : false;
            }

            for ($i = 0; $i < $c_guestbook; ++$i) {
                $guestbook[$i]['name'] = !empty($guestbook[$i]['user_name']) ? $guestbook[$i]['user_name'] : $guestbook[$i]['name'];
                $guestbook[$i]['date_formatted'] = $this->date->format($guestbook[$i]['date'], $settings['dateformat']);
                $guestbook[$i]['date_iso'] = $this->date->format($guestbook[$i]['date'], 'c');
                $guestbook[$i]['message'] = Core\Functions::nl2p($guestbook[$i]['message']);
                if ($emoticons_active === true) {
                    $guestbook[$i]['message'] = \ACP3\Modules\Emoticons\Helpers::emoticonsReplace($guestbook[$i]['message']);
                }
                $guestbook[$i]['website'] = strlen($guestbook[$i]['user_website']) > 2 ? substr($guestbook[$i]['user_website'], 0, -2) : $guestbook[$i]['website'];
                if (!empty($guestbook[$i]['website']) && (bool)preg_match('=^http(s)?://=', $guestbook[$i]['website']) === false)
                    $guestbook[$i]['website'] = 'http://' . $guestbook[$i]['website'];

                $guestbook[$i]['mail'] = !empty($guestbook[$i]['user_mail']) ? substr($guestbook[$i]['user_mail'], 0, -2) : $guestbook[$i]['mail'];
            }
            $this->view->assign('guestbook', $guestbook);
        }
    }

}