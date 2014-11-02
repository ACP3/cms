<?php

namespace ACP3\Modules\Guestbook\Controller;

use ACP3\Core;
use ACP3\Modules\Guestbook;

/**
 * Class Index
 * @package ACP3\Modules\Guestbook\Controller
 */
class Index extends Core\Modules\Controller\Frontend
{
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var Core\Pagination
     */
    protected $pagination;
    /**
     * @var Core\Helpers\Secure
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
        Core\Context\Frontend $context,
        Core\Date $date,
        Core\Pagination $pagination,
        Core\Helpers\Secure $secureHelper,
        Guestbook\Model $guestbookModel,
        Core\Config $guestbookConfig)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->secureHelper = $secureHelper;
        $this->guestbookModel = $guestbookModel;
        $this->guestbookConfig = $guestbookConfig;
    }

    public function actionCreate()
    {
        $settings = $this->guestbookConfig->getSettings();
        $hasNewsletterAccess = $this->acl->hasPermission('frontend/newsletter') === true && $settings['newsletter_integration'] == 1;

        $overlayIsActive = false;
        if ($this->request->getIsAjax() === true) {
            $this->setContentTemplate('Guestbook/index.create_ajax.tpl');
            $this->setLayout('Guestbook/ajax.tpl');
        }

        if (empty($_POST) === false) {
            $this->_createPost($_POST, $settings, $overlayIsActive, $hasNewsletterAccess);
        }

        // Emoticons einbinden
        if ($settings['emoticons'] == 1 && $this->modules->isActive('emoticons') === true) {
            $this->view->assign('emoticons', $this->get('emoticons.helpers')->emoticonsList());
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

        if ($this->acl->hasPermission('frontend/captcha/index/image') === true) {
            $this->view->assign('captcha', $this->get('captcha.helpers')->captcha());
        }

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionIndex()
    {
        $settings = $this->guestbookConfig->getSettings();
        $this->view->assign('overlay', $settings['overlay']);

        $guestbook = $this->guestbookModel->getAll($settings['notify'], POS, $this->auth->entries);
        $c_guestbook = count($guestbook);

        if ($c_guestbook > 0) {
            $this->pagination->setTotalResults($this->guestbookModel->countAll($settings['notify']));
            $this->pagination->display();

            // Emoticons einbinden
            $emoticonsActive = false;
            if ($settings['emoticons'] == 1) {
                $emoticonsActive = $this->modules->isActive('emoticons') === true && $settings['emoticons'] == 1 ? true : false;
            }

            for ($i = 0; $i < $c_guestbook; ++$i) {
                $guestbook[$i]['name'] = !empty($guestbook[$i]['user_name']) ? $guestbook[$i]['user_name'] : $guestbook[$i]['name'];
                if ($emoticonsActive === true) {
                    $guestbook[$i]['message'] = $this->get('emoticons.helpers')->emoticonsReplace($guestbook[$i]['message']);
                }
                $guestbook[$i]['website'] = strlen($guestbook[$i]['user_website']) > 2 ? substr($guestbook[$i]['user_website'], 0, -2) : $guestbook[$i]['website'];
                if (!empty($guestbook[$i]['website']) && (bool)preg_match('=^http(s)?://=', $guestbook[$i]['website']) === false)
                    $guestbook[$i]['website'] = 'http://' . $guestbook[$i]['website'];

                $guestbook[$i]['mail'] = !empty($guestbook[$i]['user_mail']) ? substr($guestbook[$i]['user_mail'], 0, -2) : $guestbook[$i]['mail'];
            }
            $this->view->assign('guestbook', $guestbook);
            $this->view->assign('dateformat', $settings['dateformat']);
        }
    }

    private function _createPost(array $formData, array $settings, $overlayIsActive, $hasNewsletterAccess)
    {
        try {
            $validator = $this->get('guestbook.validator');
            $validator->validateCreate($formData, $hasNewsletterAccess);

            $insertValues = array(
                'id' => '',
                'date' => $this->date->toSQL(),
                'ip' => $_SERVER['REMOTE_ADDR'],
                'name' => Core\Functions::strEncode($formData['name']),
                'user_id' => $this->auth->isUser() ? $this->auth->getUserId() : '',
                'message' => Core\Functions::strEncode($formData['message']),
                'website' => Core\Functions::strEncode($formData['website']),
                'mail' => $formData['mail'],
                'active' => $settings['notify'] == 2 ? 0 : 1,
            );

            $lastId = $this->guestbookModel->insert($insertValues);

            // E-Mail-Benachrichtigung bei neuem Eintrag der hinterlegten
            // E-Mail-Adresse zusenden
            if ($settings['notify'] == 1 || $settings['notify'] == 2) {
                $host = 'http://' . htmlentities($_SERVER['HTTP_HOST']);
                $fullPath = $host . $this->router->route('guestbook') . '#gb-entry-' . $lastId;
                $body = sprintf($settings['notify'] == 1 ? $this->lang->t('guestbook', 'notification_email_body_1') : $this->lang->t('guestbook', 'notification_email_body_2'), $host, $fullPath);
                $this->get('core.functions')->generateEmail('', $settings['notify_email'], $settings['notify_email'], $this->lang->t('guestbook', 'notification_email_subject'), $body);
            }

            // Falls es der Benutzer ausgewählt hat, diesen in den Newsletter eintragen
            if ($hasNewsletterAccess === true && isset($formData['subscribe_newsletter']) && $formData['subscribe_newsletter'] == 1) {
                $this->get('newsletter.helpers')->subscribeToNewsletter($formData['mail']);
            }

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'), 'guestbook', (bool)$overlayIsActive);
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'guestbook');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }

    }

}