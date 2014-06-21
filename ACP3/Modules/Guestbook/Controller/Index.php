<?php

namespace ACP3\Modules\Guestbook\Controller;

use ACP3\Core;
use ACP3\Modules\Guestbook;

/**
 * Description of GuestbookFrontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller
{
    /**
     *
     * @var Guestbook\Model
     */
    protected $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new Guestbook\Model($this->db, $this->lang);
    }

    public function actionCreate()
    {
        $settings = Core\Config::getSettings('guestbook');
        $hasNewsletterAccess = Core\Modules::hasPermission('frontend/newsletter') === true && $settings['newsletter_integration'] == 1;

        $overlayIsActive = false;
        if ($this->uri->getIsAjax() === true) {
            $this->setContentTemplate('Guestbook/index.create_ajax.tpl');
            $this->setLayout('Guestbook/ajax.tpl');
        }

        if (empty($_POST) === false) {
            try {
                $validator = new Guestbook\Validator($this->lang, $this->auth, $this->date, $this->db, $this->model);
                $validator->validateCreate($_POST, $hasNewsletterAccess);

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
                    $fullPath = $host . $this->uri->route('guestbook') . '#gb-entry-' . $this->db->lastInsertId();
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

        $defaults = array(
            'name' => '',
            'name_disabled' => '',
            'mail' => '',
            'mail_disabled' => '',
            'website' => '',
            'website_disabled' => '',
            'message' => '',
        );

        // Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
        if ($this->auth->isUser() === true) {
            $users = $this->auth->getUserInfo();
            $disabled = ' readonly="readonly" class="readonly"';
            $defaults['name'] = $users['nickname'];
            $defaults['name_disabled'] = $disabled;
            $defaults['mail'] = $users['mail'];
            $defaults['mail_disabled'] = $disabled;
            $defaults['website'] = $users['website'];
            $defaults['website_disabled'] = !empty($users['website']) ? $disabled : '';
        }

        $this->view->assign('form', array_merge($defaults, $_POST));

        if (Core\Modules::hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', \ACP3\Modules\Captcha\Helpers::captcha());
        }

        $this->session->generateFormToken();
    }

    public function actionIndex()
    {
        Core\Functions::getRedirectMessage();

        $settings = Core\Config::getSettings('guestbook');
        $this->view->assign('overlay', $settings['overlay']);

        $guestbook = $this->model->getAll($settings['notify'], POS, $this->auth->entries);
        $c_guestbook = count($guestbook);

        if ($c_guestbook > 0) {
            $pagination = new Core\Pagination(
                $this->auth,
                $this->breadcrumb,
                $this->lang,
                $this->seo,
                $this->uri,
                $this->view,
                $this->model->countAll($settings['notify'])
            );
            $pagination->display();

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