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
     * @var array
     */
    protected $guestbookSettings;
    /**
     * @var \ACP3\Core\Config
     */
    protected $seoConfig;
    /**
     * @var bool
     */
    private $emoticonsActive;
    /**
     * @var bool
     */
    private $newsletterActive;

    /**
     * @param \ACP3\Core\Context\Frontend   $context
     * @param \ACP3\Core\Date               $date
     * @param \ACP3\Core\Pagination         $pagination
     * @param \ACP3\Core\Helpers\Secure     $secureHelper
     * @param \ACP3\Modules\Guestbook\Model $guestbookModel
     * @param \ACP3\Core\Config             $guestbookConfig
     * @param \ACP3\Core\Config             $seoConfig
     */
    public function __construct(
        Core\Context\Frontend $context,
        Core\Date $date,
        Core\Pagination $pagination,
        Core\Helpers\Secure $secureHelper,
        Guestbook\Model $guestbookModel,
        Core\Config $guestbookConfig,
        Core\Config $seoConfig)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->secureHelper = $secureHelper;
        $this->guestbookModel = $guestbookModel;
        $this->guestbookSettings = $guestbookConfig->getSettings();
        $this->seoConfig = $seoConfig;

        $this->emoticonsActive = ($this->guestbookSettings['emoticons'] == 1 && $this->modules->isActive('emoticons') === true);
        $this->newsletterActive = ($this->guestbookSettings['newsletter_integration'] == 1 && $this->acl->hasPermission('frontend/newsletter') === true);
    }

    public function actionCreate()
    {
        if (empty($_POST) === false) {
            $this->_createPost($_POST);
        }

        // Emoticons einbinden
        if ($this->emoticonsActive === true) {
            $this->view->assign('emoticons', $this->get('emoticons.helpers')->emoticonsList());
        }

        // In Newsletter integrieren
        if ($this->newsletterActive === true) {
            $this->view->assign('subscribe_newsletter', $this->get('core.helpers.forms')->selectEntry('subscribe_newsletter', '1', '1', 'checked'));
            $this->view->assign('LANG_subscribe_to_newsletter', sprintf($this->lang->t('guestbook', 'subscribe_to_newsletter'), $this->seoConfig->getSettings()['title']));
        }

        $defaults = [
            'name' => '',
            'name_disabled' => '',
            'mail' => '',
            'mail_disabled' => '',
            'website' => '',
            'website_disabled' => '',
            'message' => '',
        ];

        // Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
        if ($this->auth->isUser() === true) {
            $users = $this->auth->getUserInfo();
            $disabled = ' readonly="readonly"';
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
        $this->view->assign('overlay', $this->guestbookSettings['overlay']);

        $guestbook = $this->guestbookModel->getAll($this->guestbookSettings['notify'], POS, $this->auth->entries);
        $c_guestbook = count($guestbook);

        if ($c_guestbook > 0) {
            $this->pagination->setTotalResults($this->guestbookModel->countAll($this->guestbookSettings['notify']));
            $this->pagination->display();

            for ($i = 0; $i < $c_guestbook; ++$i) {
                $guestbook[$i]['name'] = !empty($guestbook[$i]['user_name']) ? $guestbook[$i]['user_name'] : $guestbook[$i]['name'];
                if ($this->emoticonsActive === true) {
                    $guestbook[$i]['message'] = $this->get('emoticons.helpers')->emoticonsReplace($guestbook[$i]['message']);
                }
                $guestbook[$i]['website'] = strlen($guestbook[$i]['user_website']) > 2 ? substr($guestbook[$i]['user_website'], 0, -2) : $guestbook[$i]['website'];
                if (!empty($guestbook[$i]['website']) && (bool)preg_match('=^http(s)?://=', $guestbook[$i]['website']) === false) {
                    $guestbook[$i]['website'] = 'http://' . $guestbook[$i]['website'];
                }

                $guestbook[$i]['mail'] = !empty($guestbook[$i]['user_mail']) ? substr($guestbook[$i]['user_mail'], 0, -2) : $guestbook[$i]['mail'];
            }
            $this->view->assign('guestbook', $guestbook);
            $this->view->assign('dateformat', $this->guestbookSettings['dateformat']);
        }
    }

    /**
     * @param array $formData
     */
    private function _createPost(array $formData)
    {
        try {
            $validator = $this->get('guestbook.validator');
            $validator->validateCreate($formData, $this->newsletterActive);

            $insertValues = [
                'id' => '',
                'date' => $this->date->toSQL(),
                'ip' => $_SERVER['REMOTE_ADDR'],
                'name' => Core\Functions::strEncode($formData['name']),
                'user_id' => $this->auth->isUser() ? $this->auth->getUserId() : '',
                'message' => Core\Functions::strEncode($formData['message']),
                'website' => Core\Functions::strEncode($formData['website']),
                'mail' => $formData['mail'],
                'active' => $this->guestbookSettings['notify'] == 2 ? 0 : 1,
            ];

            $lastId = $this->guestbookModel->insert($insertValues);

            // E-Mail-Benachrichtigung bei neuem Eintrag der hinterlegten
            // E-Mail-Adresse zusenden
            if ($this->guestbookSettings['notify'] == 1 || $this->guestbookSettings['notify'] == 2) {
                $host = 'http://' . htmlentities($_SERVER['HTTP_HOST']);
                $fullPath = $host . $this->router->route('guestbook') . '#gb-entry-' . $lastId;
                $body = sprintf($this->guestbookSettings['notify'] == 1 ? $this->lang->t('guestbook', 'notification_email_body_1') : $this->lang->t('guestbook', 'notification_email_body_2'), $host, $fullPath);
                $this->get('core.helpers.sendEmail')->execute('', $this->guestbookSettings['notify_email'], $this->guestbookSettings['notify_email'], $this->lang->t('guestbook', 'notification_email_subject'), $body);
            }

            // Falls es der Benutzer ausgewählt hat, diesen in den Newsletter eintragen
            if ($this->newsletterActive === true && isset($formData['subscribe_newsletter']) && $formData['subscribe_newsletter'] == 1) {
                $this->get('newsletter.helpers')->subscribeToNewsletter($formData['mail']);
            }

            $this->secureHelper->unsetFormToken($this->request->query);

            $this->redirectMessages()->setMessage($lastId, $this->lang->t('system', $lastId !== false ? 'create_success' : 'create_error'), 'guestbook');
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), 'guestbook');
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
